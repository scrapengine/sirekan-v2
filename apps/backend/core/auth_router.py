from fastapi import APIRouter, Depends, HTTPException, status, Response, Request
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, delete
from core.database import get_db
from core.models import User, OTP
from core.security import verify_password, get_password_hash, create_access_token, create_refresh_token, decode_token
from core.schemas import UserLogin, UserResponse, UserCreate
from core.telegram import send_telegram_message, generate_otp, otp_expires_at
from core.audit import log_action
from typing import Optional
from datetime import datetime

router = APIRouter(prefix="/auth", tags=["auth"])

@router.post("/request-otp")
async def request_otp(telegram_id: str, type: str, db: AsyncSession = Depends(get_db)):
    # type: 'register' or 'reset'
    code = generate_otp()
    expires = otp_expires_at()
    
    # Save OTP to DB (cleanup old first)
    await db.execute(delete(OTP).where(OTP.telegram_id == telegram_id, OTP.type == type))
    
    new_otp = OTP(telegram_id=telegram_id, code=code, type=type, expires_at=expires)
    db.add(new_otp)
    await db.commit()
    
    msg = f"[SIREKAN]\nKODE OTP: {code}\nBerlaku selama 5 menit. Jangan berikan kode ini kepada siapapun."
    sent = await send_telegram_message(telegram_id, msg)
    
    if not sent:
        raise HTTPException(status_code=500, detail="Gagal mengirim OTP. Pastikan sudah /start @sirekan_robot")
        
    return {"message": "OTP terkirim ke Telegram"}

@router.post("/register", response_model=UserResponse)
async def register(user_data: UserCreate, db: AsyncSession = Depends(get_db)):
    # Verify OTP
    result = await db.execute(select(OTP).where(
        OTP.telegram_id == user_data.telegram_id, 
        OTP.code == user_data.otp_code, 
        OTP.type == 'register',
        OTP.expires_at >= datetime.utcnow()
    ))
    otp = result.scalar_one_or_none()
    if not otp:
        raise HTTPException(status_code=400, detail="OTP salah atau kadaluarsa")

    # Check if user already exists
    result = await db.execute(select(User).where((User.username == user_data.username) | (User.telegram_id == user_data.telegram_id)))
    if result.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Username atau Telegram ID sudah terdaftar")
    
    new_user = User(
        username=user_data.username,
        telegram_id=user_data.telegram_id,
        hashed_password=get_password_hash(user_data.password),
        role=user_data.role
    )
    db.add(new_user)
    await db.execute(delete(OTP).where(OTP.id == otp.id)) # Consumed
    await db.commit()
    await db.refresh(new_user)
    await log_action(db, new_user.id, "CREATE", "users", new_user.id, f"User {user_data.username} registered")
    return new_user

@router.post("/login")
async def login(response: Response, user_data: UserLogin, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(User).where(User.username == user_data.username))
    user = result.scalar_one_or_none()
    
    if not user or not verify_password(user_data.password, user.hashed_password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Username atau password salah",
        )
    
    if not user.is_active:
        raise HTTPException(status_code=403, detail="User tidak aktif")
    
    access_token = create_access_token(data={"sub": user.username, "role": user.role})
    refresh_token = create_refresh_token(data={"sub": user.username})
    
    response.set_cookie(
        key="refresh_token",
        value=refresh_token,
        httponly=True,
        secure=False,
        samesite="none",
        max_age=30 * 24 * 60 * 60
    )
    
    # Notify Login
    if user.telegram_id:
        await send_telegram_message(user.telegram_id, f"[SIREKAN]\nLogin berhasil pada {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}.\nJika bukan Anda, segera amankan akun.")
    
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": {
            "id": user.id,
            "username": user.username,
            "telegram_id": user.telegram_id,
            "role": user.role
        }
    }

@router.post("/reset-password")
async def reset_password(telegram_id: str, otp_code: str, new_password: str, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(OTP).where(
        OTP.telegram_id == telegram_id, 
        OTP.code == otp_code, 
        OTP.type == 'reset',
        OTP.expires_at >= datetime.utcnow()
    ))
    otp = result.scalar_one_or_none()
    if not otp:
        raise HTTPException(status_code=400, detail="OTP salah atau kadaluarsa")
        
    result = await db.execute(select(User).where(User.telegram_id == telegram_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
        
    user.hashed_password = get_password_hash(new_password)
    await db.execute(delete(OTP).where(OTP.id == otp.id))
    await db.commit()

    await log_action(db, user.id, "UPDATE", "users", user.id, f"Password reset for {user.username}")

    await send_telegram_message(telegram_id, "[SIREKAN]\nPassword berhasil diubah.")
    return {"message": "Password berhasil diubah"}

@router.post("/logout")
async def logout(response: Response):
    response.delete_cookie("refresh_token")
    return {"message": "Logged out"}

async def get_user_from_token(token: str, db: AsyncSession = Depends(get_db)) -> User:
    payload = decode_token(token)
    if not payload:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid token")
    
    username = payload.get("sub")
    result = await db.execute(select(User).where(User.username == username))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="User not found")
    return user

async def get_current_user(request: Request, db: AsyncSession = Depends(get_db)):
    auth_header = request.headers.get("Authorization")
    if not auth_header or not auth_header.startswith("Bearer "):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Not authenticated")
    
    token = auth_header.split(" ")[1]
    return await get_user_from_token(token=token, db=db)

@router.get("/me", response_model=UserResponse)
async def get_me(current_user: User = Depends(get_current_user)):
    return current_user
