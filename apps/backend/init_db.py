import asyncio
from core.database import engine, Base
from assurance.models import AssuranceTicket
from master_data.models_nodeb import NodeB

async def init_db():
    async with engine.begin() as conn:
        await conn.run_sync(Base.metadata.create_all)
        print("Schema created")

if __name__ == "__main__":
    asyncio.run(init_db())
