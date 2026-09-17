from fastapi import Depends
from sqlalchemy.ext.asyncio import AsyncSession
from core.database import get_db
from core.models import AuditLog

async def log_action(db: AsyncSession, user_id: int, action: str, target_table: str, target_id: int, details: str = None):
    audit_entry = AuditLog(
        user_id=user_id,
        action=action,
        target_table=target_table,
        target_id=target_id,
        details=details
    )
    db.add(audit_entry)
    await db.commit()
