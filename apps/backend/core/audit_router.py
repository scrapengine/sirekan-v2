import json
from typing import Optional
from fastapi import APIRouter, Depends, Query, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, desc, func
from core.database import get_db
from core.models import AuditLog, User

router = APIRouter(prefix="/admin/audit-logs", tags=["audit-logs"])

@router.get("")
async def list_audit_logs(
    db: AsyncSession = Depends(get_db),
    page: int = Query(1, ge=1),
    limit: int = Query(25, ge=1, le=100),
    module: Optional[str] = None,
    action: Optional[str] = None,
    search: Optional[str] = None
):
    query = select(AuditLog, User.username).outerjoin(User, AuditLog.user_id == User.id)
    
    if module:
        query = query.where(AuditLog.module == module)
    if action:
        query = query.where(AuditLog.action == action)
    if search:
        query = query.where(AuditLog.details.ilike(f"%{search}%"))
        
    total_query = select(func.count()).select_from(query.subquery())
    total = (await db.execute(total_query)).scalar()
    
    query = query.order_by(desc(AuditLog.created_at)).offset((page - 1) * limit).limit(limit)
    results = (await db.execute(query)).all()
    
    data = []
    for log, username in results:
        data.append({
            "id": log.id,
            "username": username or "System",
            "action": log.action,
            "module": log.module,
            "entity_id": log.entity_id,
            "details": log.details,
            "created_at": log.created_at
        })
        
    return {"data": data, "meta": {"total": total, "total_pages": (total + limit - 1) // limit}}

@router.get("/{log_id}")
async def get_audit_log_detail(
    log_id: int,
    db: AsyncSession = Depends(get_db)
):
    result = await db.execute(select(AuditLog).where(AuditLog.id == log_id))
    log = result.scalar_one_or_none()
    if not log:
        raise HTTPException(status_code=404, detail="Audit log not found")
    
    # Parse JSON details
    old_values = {}
    new_values = {}
    try:
        old_values = json.loads(log.old_values) if log.old_values else {}
        new_values = json.loads(log.new_values) if log.new_values else {}
    except (json.JSONDecodeError, AttributeError):
        old_values = {}
        new_values = {}
    
    # Compute diff
    diff = {}
    all_keys = set(list(old_values.keys()) + list(new_values.keys()))
    for key in sorted(all_keys):
        old_val = old_values.get(key)
        new_val = new_values.get(key)
        if key not in old_values:
            diff[key] = {"status": "added", "old": None, "new": new_val}
        elif key not in new_values:
            diff[key] = {"status": "removed", "old": old_val, "new": None}
        elif old_val != new_val:
            diff[key] = {"status": "changed", "old": old_val, "new": new_val}
    
    return {
        "id": log.id,
        "username": log.user.username if log.user else "System",
        "action": log.action,
        "module": log.module,
        "entity_id": log.entity_id,
        "details": log.details,
        "old_values": old_values,
        "new_values": new_values,
        "diff": diff,
        "created_at": log.created_at,
        "ip_address": log.ip_address
    }