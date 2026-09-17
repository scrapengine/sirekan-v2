import asyncio
from core.database import engine, Base
from core.models import User, OTP

async def sync_db():
    async with engine.begin() as conn:
        # Create all tables that don't exist
        await conn.run_sync(Base.metadata.create_all)
    print("Database sync complete: Tables verified/created.")

if __name__ == "__main__":
    asyncio.run(sync_db())
