from fastapi import APIRouter, Depends, HTTPException, status, UploadFile, File
from sqlalchemy.ext.asyncio import AsyncSession
import openpyxl
from io import BytesIO
from sqlalchemy import select, delete
from typing import Optional
from core.database import get_db
from core.models import User
from core.security import get_password_hash
from core.schemas import UserOut, UserCreateAdmin, UserUpdateAdmin
from core.deps import RoleChecker
from core.audit import log_action
from datetime import datetime

router = APIRouter(prefix="/admin", tags=["admin"])

# Dependency for admin-only
admin_only = RoleChecker(["administrator"])


@router.get("/users", response_model=dict)
async def list_users(
    page: int = 1,
    limit: int = 25,
    search: str = "",
    sort_by: str = "id",
    order: str = "desc",
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    # Build query
    query = select(User)
    if search:
        query = query.where(
            (User.username.ilike(f"%{search}%"))
            | (User.email.ilike(f"%{search}%"))
            | (User.telegram_id.ilike(f"%{search}%"))
        )
    # Sorting
    sort_column = getattr(User, sort_by, User.id)
    if order.lower() == "asc":
        query = query.order_by(sort_column.asc())
    else:
        query = query.order_by(sort_column.desc())
    # Pagination
    offset = (page - 1) * limit
    total_result = await db.execute(select(User))
    total = len(total_result.scalars().all())
    query = query.offset(offset).limit(limit)
    result = await db.execute(query)
    users = result.scalars().all()
    return {
        "meta": {
            "total": total,
            "page": page,
            "limit": limit,
            "total_pages": (total + limit - 1) // limit if limit > 0 else 1,
        },
        "data": [UserOut.from_orm(u) for u in users],
    }


@router.get("/users/{user_id}", response_model=UserOut)
async def get_user(
    user_id: int,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return UserOut.from_orm(user)


@router.post("/users", response_model=UserOut, status_code=status.HTTP_201_CREATED)
async def create_user(
    data: UserCreateAdmin,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    # Check duplicates
    existing = await db.execute(
        select(User).where(
            (User.username == data.username)
            | (User.telegram_id == data.telegram_id)
        )
    )
    if existing.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Username or Telegram ID already exists")
    new_user = User(
        username=data.username,
        email=data.email,
        telegram_id=data.telegram_id,
        hashed_password=get_password_hash(data.password),
        role=data.role,
        is_active=data.is_active,
    )
    db.add(new_user)
    await db.commit()
    await db.refresh(new_user)
    await log_action(db, current_user.id, "CREATE", "users", new_user.id, f"Admin created user {data.username}")
    return UserOut.from_orm(new_user)


@router.patch("/users/{user_id}", response_model=UserOut)
async def update_user(
    user_id: int,
    data: UserUpdateAdmin,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    # Update fields if provided
    if data.email is not None:
        user.email = data.email
    if data.telegram_id is not None:
        user.telegram_id = data.telegram_id
    if data.role is not None:
        user.role = data.role
    if data.is_active is not None:
        user.is_active = data.is_active
    if data.password:
        user.hashed_password = get_password_hash(data.password)
    await db.commit()
    await db.refresh(user)
    await log_action(db, current_user.id, "UPDATE", "users", user.id, f"Admin updated user {user.username}")
    return UserOut.from_orm(user)


@router.delete("/users/{user_id}")
async def delete_user(
    user_id: int,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    # Prevent deleting self
    if user.id == current_user.id:
        raise HTTPException(status_code=400, detail="Cannot delete yourself")
    await db.execute(delete(User).where(User.id == user_id))
    await db.commit()
    await log_action(db, current_user.id, "DELETE", "users", user_id, f"Admin deleted user {user.username}")
    return {"message": "deleted"}


@router.post("/users/import", response_model=dict)
async def import_users(
    file: UploadFile = File(...),
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(admin_only),
):
    if not file.filename.endswith(('.xlsx', '.xls')):
        raise HTTPException(status_code=400, detail="Only Excel files (.xlsx, .xls) are supported")
    
    contents = await file.read()
    try:
        wb = openpyxl.load_workbook(filename=BytesIO(contents), data_only=True)
    except Exception:
        raise HTTPException(status_code=400, detail="Invalid Excel file")
    
    sheet = wb.active
    rows = list(sheet.iter_rows(values_only=True))
    if not rows or len(rows) < 2:
        raise HTTPException(status_code=400, detail="Excel file is empty or missing data")
    
    headers = [str(h).strip().lower() for h in rows[0] if h is not None]
    required = ["username", "email", "role"]
    for req in required:
        if req not in headers:
            raise HTTPException(status_code=400, detail=f"Missing required column: {req}")
    
    username_idx = headers.index("username")
    email_idx = headers.index("email")
    role_idx = headers.index("role")
    telegram_idx = headers.index("telegram_id") if "telegram_id" in headers else -1
    password_idx = headers.index("password") if "password" in headers else -1
    
    success_count = 0
    errors = []
    
    for idx, row in enumerate(rows[1:], start=2):
        if not any(row):
            continue # Skip empty rows
        
        try:
            username = str(row[username_idx]).strip() if username_idx < len(row) and row[username_idx] is not None else ""
            email = str(row[email_idx]).strip() if email_idx < len(row) and row[email_idx] is not None else ""
            role = str(row[role_idx]).strip().lower() if role_idx < len(row) and row[role_idx] is not None else "viewer"
            telegram_id = str(row[telegram_idx]).strip() if telegram_idx != -1 and telegram_idx < len(row) and row[telegram_idx] is not None else ""
            password = str(row[password_idx]).strip() if password_idx != -1 and password_idx < len(row) and row[password_idx] is not None else "password123"
            
            if not username or not email:
                errors.append(f"Row {idx}: Username and email are required")
                continue
            
            if role not in ["administrator", "operator", "viewer"]:
                errors.append(f"Row {idx}: Invalid role '{role}'")
                continue
            
            # Check existing
            existing = await db.execute(select(User).where((User.username == username) | (User.email == email)))
            if existing.scalar_one_or_none():
                errors.append(f"Row {idx}: Username or email already exists")
                continue
            
            new_user = User(
                username=username,
                email=email,
                role=role,
                telegram_id=telegram_id if telegram_id else None,
                hashed_password=get_password_hash(password),
                is_active=True
            )
            db.add(new_user)
            success_count += 1
        except Exception as e:
            errors.append(f"Row {idx}: {str(e)}")
            
    await db.commit()
    await log_action(db, current_user.id, "IMPORT", "users", 0, f"Imported {success_count} users from Excel")
    
    return {
        "message": f"Successfully imported {success_count} users",
        "imported": success_count,
        "errors": errors
    }