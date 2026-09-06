import asyncio
from sqlalchemy.ext.asyncio import AsyncSession
from core.database import SessionLocal
from assurance.models import AssuranceTicket

async def seed_data():
    async with SessionLocal() as session:
        tickets = [
            AssuranceTicket(incident="INC001", customer="PT Maju Jaya", status="NEW", witel="Witel Jakarta", reported_date="2026-09-01"),
            AssuranceTicket(incident="INC002", customer="Toko Sembako", status="ANALYSIS", witel="Witel Bandung", reported_date="2026-09-02"),
            AssuranceTicket(incident="INC003", customer="Warung Makan", status="BACKEND", witel="Witel Surabaya", reported_date="2026-09-03"),
            AssuranceTicket(incident="INC004", customer="Kios Pulsa", status="PENDING", witel="Witel Medan", reported_date="2026-09-03"),
            AssuranceTicket(incident="INC005", customer="Kantor Desa", status="DRAFT", witel="Witel Makassar", reported_date="2026-09-04")
        ]
        session.add_all(tickets)
        await session.commit()
        print("5 sample tickets seeded.")

if __name__ == "__main__":
    asyncio.run(seed_data())
