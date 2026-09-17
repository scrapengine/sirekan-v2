import asyncio
from sqlalchemy import text
from core.database import engine
from core.models import Base

async def add_audit_log_columns():
    async with engine.begin() as conn:
        await conn.run_sync(lambda sync_conn: sync_conn.execute(text('ALTER TABLE audit_logs ADD COLUMN old_values TEXT')))
        await conn.run_sync(lambda sync_conn: sync_conn.execute(text('ALTER TABLE audit_logs ADD COLUMN new_values TEXT')))
        await conn.run_sync(lambda sync_conn: sync_conn.execute(text('ALTER TABLE audit_logs ADD COLUMN ip_address VARCHAR')))
    print('AuditLog diff fields added')

if __name__ == "__main__":
    asyncio.run(add_audit_log_columns())
