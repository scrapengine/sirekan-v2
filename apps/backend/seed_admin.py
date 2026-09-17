import asyncio
from core.database import SessionLocal
from core.models import User
from core.security import get_password_hash
from sqlalchemy import select

async def create_superuser():
    async with SessionLocal() as session:
        result = await session.execute(select(User).where(User.username == "admin"))
        user = result.scalar_one_or_none()
        
        if user:
            print("Superuser already exists.")
            return
            
        new_user = User(
            username="admin",
            email="admin@sirekan.local",
            hashed_password=get_password_hash("admin123"),
            role="administrator",
            is_active=True
        )
        session.add(new_user)
        await session.commit()
        print("Superuser 'admin' created successfully (password: admin123).")

if __name__ == "__main__":
    asyncio.run(create_superuser())
