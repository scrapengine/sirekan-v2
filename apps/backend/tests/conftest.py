import os
import pytest
from fastapi.testclient import TestClient
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.orm import sessionmaker

from core.database import Base, get_db
from core.auth_router import get_current_user
from core.config import settings
from core.models import Base as BaseModel

# Use in-memory SQLite for testing
TEST_DATABASE_URL = "sqlite+aiosqlite:///:memory:"

engine = create_async_engine(TEST_DATABASE_URL, connect_args={"check_same_thread": False})
TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, class_=AsyncSession)

# Override dependency
async def override_get_db():
    async with TestingSessionLocal() as session:
        yield session

# Fixture for test client
@pytest.fixture(scope="session")
def client():
    # Create tables
    Base.metadata.create_all(bind=engine)
    with TestingSessionLocal() as session:
        # Create admin user for login test
        user = User(
            username="testadmin",
            email="test@example.com",
            telegram_id="123456789",
            hashed_password="hashed",  # In real test, we'd hash, but for simplicity
            role="administrator",
            is_active=True
        )
        session.add(user)
        session.commit()
    return TestClient(app)