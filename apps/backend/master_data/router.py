from fastapi import APIRouter, Depends, Query
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import text, update, delete, insert
from core.database import get_db
from .service import process_nodeb_record
from pydantic import BaseModel
from typing import Optional
from datetime import date

router = APIRouter(prefix="/master-data")

class NodeBUpdate(BaseModel):
    sto: str
    site_id: str
    site_name: str
    hostname_metro: str
    ip_metro: Optional[str] = None
    port_metro: str
    hostname_olt: str
    ip_olt: Optional[str] = None
    type_olt: Optional[str] = None
    port_onu: Optional[str] = None
    hostname_ont: Optional[str] = None
    ip_ont: Optional[str] = None
    ont_type: Optional[str] = None
    serial_number: Optional[str] = None
    odc: Optional[str] = None
    odp: Optional[str] = None
    tikor_site: Optional[str] = None
    on_air: Optional[str] = None
    graph_id: Optional[str] = None
    evidence: Optional[str] = None

@router.get("/nodeb")
async def get_nodeb(
    page: int = 1,
    limit: int = 25,
    search: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from}"
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name, 
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.type_olt,
        sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
    """
    data_query = f"SELECT {select_fields} {base_from}"
    if search:
        where_clause = """ AND (
            n.site_id LIKE :search OR n.site_name LIKE :search OR 
            n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search)"""
        data_query += where_clause
        count_query += where_clause
        search_param = f"%{search}%"
    else:
        search_param = ""

    data_query += " GROUP BY n.idnodeb LIMIT :limit OFFSET :offset"

    if search:
        total = (await db.execute(text(count_query), {"search": search_param})).scalar() or 0
        result = await db.execute(text(data_query), {"search": search_param, "limit": limit, "offset": offset})
    else:
        total = (await db.execute(text(count_query))).scalar() or 0
        result = await db.execute(text(data_query), {"limit": limit, "offset": offset})

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [process_nodeb_record(dict(row)) for row in result.mappings().all()]
    }

@router.get("/nodeb/trash")
async def get_trash_nodeb(
    page: int = 1,
    limit: int = 25,
    search: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NOT NULL
    """
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from}"
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name, 
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, olt.type_olt, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.ip_olt,
        sto.idsto,
        m.hostname_metro, n.deleted_at
    """
    data_query = f"SELECT {select_fields} {base_from}"
    if search:
        where_clause = """ AND (
            n.site_id LIKE :search OR n.site_name LIKE :search OR 
            n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search
        )"""
        data_query += where_clause
        count_query += where_clause
        search_param = f"%{search}%"
    else:
        search_param = ""

    data_query += " GROUP BY n.idnodeb ORDER BY n.deleted_at DESC LIMIT :limit OFFSET :offset"

    if search:
        total = (await db.execute(text(count_query), {"search": search_param})).scalar() or 0
        result = await db.execute(text(data_query), {"search": search_param, "limit": limit, "offset": offset})
    else:
        total = (await db.execute(text(count_query))).scalar() or 0
        result = await db.execute(text(data_query), {"limit": limit, "offset": offset})

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [process_nodeb_record(dict(row)) for row in result.mappings().all()]
    }

@router.get("/nodeb/{id}")
async def get_nodeb_detail(id: int, db: AsyncSession = Depends(get_db)):
    query = text("""
        SELECT 
            n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name, 
            n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
            n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
            n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
            c.graph_id, ont.merk, ont.type,
            olt.port_metro AS port_metro_olt, olt.type_olt,
            sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.idnodeb = :id
    """)
    result = await db.execute(query, {"id": id})
    row = result.mappings().first()
    if not row:
        return {"error": "Not found"}
    return process_nodeb_record(dict(row))

@router.post("/nodeb")
async def create_nodeb(data: NodeBUpdate, db: AsyncSession = Depends(get_db)):
    insert_data = data.dict(exclude={'graph_id', 'evidence'})
    stmt = insert(text("datanodeb")).values(**insert_data)
    result = await db.execute(stmt)
    new_id = result.inserted_primary_key[0]
    
    if data.graph_id:
        await db.execute(text("INSERT INTO cacti (graph_id, idnodeb) VALUES (:g, :id)"), {"g": data.graph_id, "id": new_id})
        
    await db.commit()
    return {"status": "success", "id": new_id}

@router.patch("/nodeb/{id}")
async def update_nodeb(id: int, data: NodeBUpdate, db: AsyncSession = Depends(get_db)):
    update_data = data.dict(exclude={'graph_id', 'evidence'})
    stmt = update(text("datanodeb")).where(text("idnodeb = :id")).values(**update_data)
    await db.execute(stmt, {"id": id})
    
    if data.graph_id is not None:
        await db.execute(delete(text("cacti")).where(text("idnodeb = :id")), {"id": id})
        if data.graph_id:
            await db.execute(text("INSERT INTO cacti (graph_id, idnodeb) VALUES (:g, :id)"), {"g": data.graph_id, "id": id})
            
    await db.commit()
    return {"status": "success"}

@router.post("/nodeb/restore/{id}")
async def restore_nodeb(id: int, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datanodeb SET deleted_at = NULL WHERE idnodeb = :id"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.post("/nodeb/restore-all")
async def restore_all_nodeb(db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datanodeb SET deleted_at = NULL WHERE deleted_at IS NOT NULL"))
    await db.commit()
    return {"status": "success"}

@router.delete("/nodeb/{id}")
async def delete_nodeb(id: int, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datanodeb SET deleted_at = datetime('now') WHERE idnodeb = :id"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.delete("/nodeb/purge/{id}")
async def purge_nodeb(id: int, db: AsyncSession = Depends(get_db)):
    await db.execute(text("DELETE FROM datanodeb WHERE idnodeb = :id"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.delete("/nodeb/purge-all")
async def purge_all_nodeb(db: AsyncSession = Depends(get_db)):
    await db.execute(text("DELETE FROM datanodeb WHERE deleted_at IS NOT NULL"))
    await db.commit()
    return {"status": "success"}

@router.get("/sto")
async def get_sto(db: AsyncSession = Depends(get_db)):
    result = await db.execute(text("SELECT idsto FROM datasto WHERE idsto != '-'"))
    return [r[0] for r in result.fetchall()]

@router.get("/olt")
async def get_olt(
    page: int = 1,
    limit: int = 10,
    search: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    base_from = "FROM dataolt WHERE hostname_olt != '-'"
    if search:
        where = " AND (hostname_olt LIKE :s OR ip_olt LIKE :s OR platform LIKE :s)"
        search_p = f"%{search}%"
    else:
        where = ""
        search_p = ""

    count_q = f"SELECT COUNT(*) {base_from} {where}"
    data_q = f"SELECT hostname_metro, port_metro, hostname_olt, ip_olt, platform {base_from} {where} LIMIT :limit OFFSET :offset"

    if search:
        total = (await db.execute(text(count_q), {"s": search_p})).scalar() or 0
        res = await db.execute(text(data_q), {"s": search_p, "limit": limit, "offset": offset})
    else:
        total = (await db.execute(text(count_q))).scalar() or 0
        res = await db.execute(text(data_q), {"limit": limit, "offset": offset})

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [dict(r) for r in res.mappings().all()]
    }
