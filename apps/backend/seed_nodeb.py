
import asyncio
from core.database import SessionLocal
from master_data.models_nodeb import NodeB

async def seed_nodeb():
    async with SessionLocal() as session:
        nodes = [
            NodeB(idsto="STO01", site_id="TBE006", site_name="Site Test 006", hostname_metro="metro-01", port_metro="Giga 0/1", hostname_olt="olt-01", port_onu="1/1/1", hostname_ont="ont-01", ip_ont="10.0.0.1", ont_type="ZTE", serial_number="ZTEC1234", odc="ODC-A", odp="ODP-1", tikor_site="-6.2, 106.8", on_air="2026-01-01"),
            NodeB(idsto="STO02", site_id="BDG001", site_name="Bandung Raya", hostname_metro="metro-02", port_metro="Giga 0/2", hostname_olt="olt-02", port_onu="1/1/2", hostname_ont="ont-02", ip_ont="10.0.0.2", ont_type="Huawei", serial_number="HW5678", odc="ODC-B", odp="ODP-2", tikor_site="-6.9, 107.6", on_air="2026-02-01")
        ]
        session.add_all(nodes)
        await session.commit()
        print("NodeB seeded successfully.")

if __name__ == "__main__":
    asyncio.run(seed_nodeb())
