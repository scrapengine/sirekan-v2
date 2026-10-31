import io
import pandas as pd
import openpyxl.styles
import openpyxl.utils
from datetime import datetime, date

from fastapi import APIRouter, Depends, Request, Query, HTTPException, File, UploadFile
from fastapi.responses import StreamingResponse
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import text, delete, select, update, insert, exc
from sqlalchemy.exc import IntegrityError
from core.database import get_db
from .models_infra import Metro, OLT, STO
from pydantic import BaseModel
from typing import Optional, List

router = APIRouter(prefix="/master-data")

# -------------------------- DTOs --------------------------
class MetroDTO(BaseModel):
    hostname_metro: str
    ip_metro: Optional[str] = None
    idsto: str
    # timestamps omitted (auto handled by DB)

class OLTDTO(BaseModel):
    hostname_olt: str
    witel: Optional[str] = None
    idsto: str
    hostname_metro: str
    port_metro: Optional[str] = None
    ip_olt: Optional[str] = None
    port_olt: Optional[str] = None
    platform: Optional[str] = None
    type_olt: Optional[str] = None
    lokasi: Optional[str] = None
    koordinat: Optional[str] = None
    lokasi: Optional[str] = None
    koordinat: Optional[str] = None

class STODTO(BaseModel):
    idsto: str
    nama_sto: Optional[str] = None
    witel: Optional[str] = None
    longitude: Optional[str] = None
    latitude: Optional[str] = None
    alamat: Optional[str] = None

# -------------------------- Helpers --------------------------
def _apply_filters(query, table, request: Request):
    # generic search across main columns
    keyword = request.query_params.get('keyword')
    if keyword:
        cols = [c.name for c in table.__table__.columns if c.name not in ('deleted_at', 'created_at', 'updated_at')]
        conds = [f"{table.__tablename__}.{col} LIKE :kw" for col in cols]
        query = query.where(text('(' + ' OR '.join(conds) + ')')).params(kw=f"%{keyword}%")
    
    # Dynamic choice/values filters (same pattern as NodeB router)
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')
    
    if choice and values:
        choice_clauses = []
        params = {}
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = f"{table.__tablename__}.{c}"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            query = query.where(text('(' + ' OR '.join(choice_clauses) + ')')).params(**params)
    
    return query

# -------------------------- Metro --------------------------
@router.get("/metro")
async def list_metro(request: Request, page: int = 1, limit: int = 25, search: str = "", db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(Metro.hostname_metro, Metro.ip_metro, Metro.idsto, Metro.created_at, Metro.updated_at, Metro.deleted_at).where(Metro.deleted_at.is_(None))
    if search:
        base = base.where(text("(hostname_metro LIKE :s OR ip_metro LIKE :s OR idsto LIKE :s)")).params(s=f"%{search}%")
    base = _apply_filters(base, Metro, request)
    total_stmt = select(text('COUNT(*)')).select_from(select(Metro).where(Metro.deleted_at.is_(None)).subquery())
    total = (await db.execute(total_stmt)).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    data = [dict(r) for r in rows.mappings().all()]
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": data}

@router.get("/metro/export")
async def export_metro(request: Request, db: AsyncSession = Depends(get_db)):
    from fastapi.responses import StreamingResponse
    import pandas as pd
    import io
    
    base = select(Metro.hostname_metro, Metro.ip_metro, Metro.idsto).where(Metro.deleted_at.is_(None))
    base = _apply_filters(base, Metro, request)
    rows = await db.execute(base)
    data = [dict(r) for r in rows.mappings().all()]
    
    df = pd.DataFrame(data)
    df.insert(0, 'NO', range(1, len(df) + 1))
    df = df.rename(columns={'idsto': 'STO', 'hostname_metro': 'HOSTNAME METRO', 'ip_metro': 'IP METRO'})
    
    output = io.BytesIO()
    with pd.ExcelWriter(output, engine='openpyxl') as writer:
        df.to_excel(writer, index=False, sheet_name='METRO')
    output.seek(0)
    
    filename = f'METRO-{pd.Timestamp.now().strftime("%y%m%d-%H%M%S")}.xlsx'
    return StreamingResponse(
        output,
        media_type='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        headers={'Content-Disposition': f'attachment; filename={filename}'}
    )

@router.get("/metro/trash")
async def list_metro_trash(request: Request, page: int = 1, limit: int = 25, db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(Metro.hostname_metro, Metro.ip_metro, Metro.idsto, Metro.created_at, Metro.updated_at, Metro.deleted_at).where(Metro.deleted_at.is_not(None))
    base = _apply_filters(base, Metro, request)
    total_stmt = select(text('COUNT(*)')).select_from(select(Metro).where(Metro.deleted_at.is_not(None)).subquery())
    total = (await db.execute(total_stmt)).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    data = [dict(r) for r in rows.mappings().all()]
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": data}

@router.post("/metro")
async def create_metro(dto: MetroDTO, db: AsyncSession = Depends(get_db)):
    from sqlalchemy import insert
    stmt = insert(Metro).values(**dto.dict())
    await db.execute(stmt)
    await db.commit()
    return {"msg": "created"}

@router.patch("/metro/{hostname}")
async def update_metro(hostname: str, dto: MetroDTO, db: AsyncSession = Depends(get_db)):
    stmt = update(Metro).where(Metro.hostname_metro == hostname).values(**dto.dict(exclude_unset=True))
    result = await db.execute(stmt)
    if result.rowcount == 0:
        raise HTTPException(404, "Metro not found")
    await db.commit()
    return {"msg": "updated"}

@router.post("/metro/restore/{hostname}")
async def restore_metro(hostname: str, db: AsyncSession = Depends(get_db)):
    stmt = update(Metro).where(Metro.hostname_metro == hostname).values(deleted_at=None)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "restored"}

@router.delete("/metro/purge/{hostname}")
async def purge_metro(hostname: str, db: AsyncSession = Depends(get_db)):
    stmt = delete(Metro).where(Metro.hostname_metro == hostname)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "purged"}

@router.delete("/metro/{hostname}")
async def delete_metro(hostname: str, db: AsyncSession = Depends(get_db)):
    rel = await db.execute(select(OLT).where(OLT.hostname_metro == hostname, OLT.deleted_at.is_(None)))
    if rel.first():
        raise HTTPException(400, "Cannot delete; related OLT exists")
    stmt = update(Metro).where(Metro.hostname_metro == hostname).values(deleted_at=text('CURRENT_TIMESTAMP'))
    await db.execute(stmt)
    await db.commit()
    return {"msg": "soft-deleted"}

# restore & purge (similar to existing pattern – omitted for brevity)

# -------------------------- OLT --------------------------
@router.get("/olt")
async def list_olt(request: Request, page: int = 1, limit: int = 25, db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(OLT).where(OLT.deleted_at.is_(None))
    base = _apply_filters(base, OLT, request)
    total = (await db.execute(select(text('COUNT(*)')).select_from(base.subquery()))).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": [dict(r) for r in rows.mappings().all()]}

@router.post("/olt")
async def create_olt(dto: OLTDTO, db: AsyncSession = Depends(get_db)):
    # ensure related Metro exists
    metro = await db.get(Metro, dto.hostname_metro)
    if not metro:
        raise HTTPException(400, "Related Metro not found")
    stmt = insert(OLT).values(**dto.dict())
    await db.execute(stmt)
    await db.commit()
    return {"msg": "created"}

@router.patch("/olt/{hostname}")
async def update_olt(hostname: str, dto: OLTDTO, db: AsyncSession = Depends(get_db)):
    stmt = update(OLT).where(OLT.hostname_olt == hostname).values(**dto.dict(exclude_unset=True))
    result = await db.execute(stmt)
    if result.rowcount == 0:
        raise HTTPException(404, "OLT not found")
    await db.commit()
    return {"msg": "updated"}

@router.delete("/olt/{hostname}")
async def delete_olt(hostname: str, db: AsyncSession = Depends(get_db)):
    # soft‑delete, check relation with NodeB
    rel = await db.execute(select(text('1')).where(text('EXISTS (SELECT 1 FROM datanodeb WHERE hostname_olt = :h AND deleted_at IS NULL)'), {'h': hostname}))
    if rel.fetchone():
        raise HTTPException(400, "Cannot delete; related NodeB exists")
    stmt = update(OLT).where(OLT.hostname_olt == hostname).values(deleted_at=text("datetime('now')"))
    await db.execute(stmt)
    await db.commit()
    return {"msg": "soft‑deleted"}

@router.get("/olt/{hostname}")
async def get_olt_detail(hostname: str, db: AsyncSession = Depends(get_db)):
    res = await db.get(OLT, hostname)
    if not res:
        raise HTTPException(404, "OLT not found")
    return res

@router.get("/olt/trash")
async def list_olt_trash(request: Request, page: int = 1, limit: int = 25, db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(OLT).where(OLT.deleted_at.is_not(None))
    base = _apply_filters(base, OLT, request)
    total = (await db.execute(select(text('COUNT(*)')).select_from(base.subquery()))).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": [dict(r) for r in rows.mappings().all()]}

@router.post("/olt/restore/{hostname}")
async def restore_olt(hostname: str, db: AsyncSession = Depends(get_db)):
    stmt = update(OLT).where(OLT.hostname_olt == hostname).values(deleted_at=None)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "restored"}

@router.post("/olt/restore-all")
async def restore_all_olt(db: AsyncSession = Depends(get_db)):
    stmt = update(OLT).where(OLT.deleted_at.is_not(None)).values(deleted_at=None)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "all restored"}

@router.delete("/olt/purge/{hostname}")
async def purge_olt(hostname: str, db: AsyncSession = Depends(get_db)):
    stmt = delete(OLT).where(OLT.hostname_olt == hostname)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "purged"}

@router.delete("/olt/purge-all")
async def purge_all_olt(db: AsyncSession = Depends(get_db)):
    stmt = delete(OLT).where(OLT.deleted_at.is_not(None))
    await db.execute(stmt)
    await db.commit()
    return {"msg": "all purged"}

@router.get("/olt/export")
async def export_olt(request: Request, db: AsyncSession = Depends(get_db)):
    select_fields = """
        o.hostname_olt, o.ip_olt, o.port_olt, o.platform, o.type_olt, o.witel,
        o.hostname_metro, o.port_metro, o.idsto,
        m.ip_metro, m.hostname_metro as metro_hostname
    """
    from_clause = """
        FROM dataolt o
        LEFT JOIN datametro m ON m.hostname_metro = o.hostname_metro
        WHERE o.deleted_at IS NULL AND o.hostname_olt != 'DIRECT_METRO' AND o.hostname_olt != '-'
    """
    where_clauses = []
    params = {}

    keyword = request.query_params.get('keyword')
    if keyword:
        cols = ['o.witel', 'o.idsto', 'o.hostname_metro', 'o.ip_metro', 'o.port_metro', 'o.hostname_olt', 'o.ip_olt', 'o.port_olt', 'o.platform', 'o.type_olt']
        conds = [f"{c} LIKE :kw" for c in cols]
        where_clauses.append('(' + ' OR '.join(conds) + ')')
        params['kw'] = f"%{keyword}%"

    # Dynamic filters
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')
    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = f"o.{c}" if c in ('witel','idsto','hostname_metro','port_metro','hostname_olt','ip_olt','port_olt','platform','type_olt') else f"o.{c}"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    query = text(f"SELECT {select_fields} {from_clause} {where_str}")
    result = await db.execute(query, params)
    raw_data = result.mappings().all()

    processed_data = []
    for i, d in enumerate(raw_data):
        d_dict = dict(d)
        port_metro_val = d_dict.get('port_metro') or d_dict.get('metro_port_metro')
        processed_data.append({
            'NO': i + 1,
            'WITEL': d_dict.get('witel'),
            'STO': d_dict.get('idsto'),
            'HOSTNAME_METRO': d_dict.get('hostname_metro'),
            'IP_METRO': d_dict.get('ip_metro'),
            'PORT_METRO': port_metro_val,
            'HOSTNAME_OLT': d_dict.get('hostname_olt'),
            'IP_OLT': d_dict.get('ip_olt'),
            'PORT_OLT': d_dict.get('port_olt'),
            'PLATFORM': d_dict.get('platform'),
            'TYPE': d_dict.get('type_olt'),
        })

    df = pd.DataFrame(processed_data)
    stream = io.BytesIO()
    writer = pd.ExcelWriter(stream, engine='openpyxl')
    df.to_excel(writer, index=False, sheet_name='OLT Data')
    writer.close()
    stream.seek(0)

    filename = f'OLT-{datetime.now().strftime("%y%m%d-%H%M%S")}.xlsx'
    return StreamingResponse(
        stream,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": f"attachment; filename=\"{filename}\""}
    )

@router.post("/olt/import")
async def import_olt(file_excel: UploadFile = File(...), db: AsyncSession = Depends(get_db)):
    if not file_excel.filename.endswith(('.xlsx', '.xls')):
        raise HTTPException(400, "Format File Tidak Sesuai")
    try:
        content = await file_excel.read()
        df = pd.read_excel(io.BytesIO(content), header=None)
        async with db.begin():
            for i, row in df.iterrows():
                if i == 0: continue  # Skip header
                val = row.tolist()
                data = {
                    'witel': str(val[0]) if pd.notnull(val[0]) else '',
                    'idsto': str(val[1]) if pd.notnull(val[1]) else '',
                    'hostname_metro': str(val[2]) if pd.notnull(val[2]) else '',
                    'port_metro': str(val[3]) if pd.notnull(val[3]) else '',
                    'hostname_olt': str(val[4]) if pd.notnull(val[4]) else '',
                    'ip_olt': str(val[5]) if pd.notnull(val[5]) else '',
                    'port_olt': str(val[6]) if pd.notnull(val[6]) else '',
                    'platform': str(val[7]) if pd.notnull(val[7]) else '',
                    'type_olt': str(val[8]) if pd.notnull(val[8]) else '',
                }
                if not data['hostname_olt']: continue
                # Upsert based on legacy: check if hostname_olt exists
                existing = (await db.execute(select(OLT).where(OLT.hostname_olt == data['hostname_olt']))).scalar_one_or_none()
                if existing:
                    await db.execute(update(OLT).where(OLT.hostname_olt == data['hostname_olt']).values(**data))
                else:
                    await db.execute(insert(OLT).values(**data))
        return {"status": "success", "message": "Import berhasil"}
    except Exception as e:
        raise HTTPException(500, str(e))
@router.get("/sto")
async def list_sto(request: Request, page: int = 1, limit: int = 25, db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(STO).where(STO.deleted_at.is_(None))
    base = _apply_filters(base, STO, request)
    total = (await db.execute(select(text('COUNT(*)')).select_from(base.subquery()))).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": [dict(r) for r in rows.mappings().all()]}

@router.post("/sto")
async def create_sto(dto: STODTO, db: AsyncSession = Depends(get_db)):
    # Validate required fields
    if not dto.idsto or not dto.idsto.strip():
        raise HTTPException(400, "ID STO (idsto) is required")
    if not dto.nama_sto or not dto.nama_sto.strip():
        raise HTTPException(400, "Nama STO (nama_sto) is required")
    try:
        stmt = insert(STO).values(**dto.dict())
        await db.execute(stmt)
        await db.commit()
        return {"msg": "created"}
    except IntegrityError:
        raise HTTPException(409, f"STO with idsto '{dto.idsto}' already exists")

@router.patch("/sto/{idsto}")
async def update_sto(idsto: str, dto: STODTO, db: AsyncSession = Depends(get_db)):
    # Validate required fields
    if dto.nama_sto is not None and not dto.nama_sto.strip():
        raise HTTPException(400, "Nama STO (nama_sto) cannot be empty")
    stmt = update(STO).where(STO.idsto == idsto).values(**dto.dict(exclude_unset=True))
    result = await db.execute(stmt)
    if result.rowcount == 0:
        raise HTTPException(404, "STO not found")
    await db.commit()
    return {"msg": "updated"}

@router.get("/sto/{idsto}")
async def get_sto_detail(idsto: str, db: AsyncSession = Depends(get_db)):
    res = await db.get(STO, idsto)
    if not res:
        raise HTTPException(404, "STO not found")
    return res

@router.delete("/sto/{idsto}")
async def delete_sto(idsto: str, db: AsyncSession = Depends(get_db)):
    # soft‑delete, check relation with OLT
    rel = await db.execute(select(text('1')).where(text('EXISTS (SELECT 1 FROM dataolt WHERE idsto = :s AND deleted_at IS NULL)'), {'s': idsto}))
    if rel.fetchone():
        raise HTTPException(400, "Cannot delete; related OLT exists")
    stmt = update(STO).where(STO.idsto == idsto).values(deleted_at=text("datetime('now')"))
    await db.execute(stmt)
    await db.commit()
    return {"msg": "soft‑deleted"}

@router.get("/sto/trash")
async def list_trash_sto(request: Request, page: int = 1, limit: int = 25, db: AsyncSession = Depends(get_db)):
    offset = (page - 1) * limit
    base = select(STO).where(STO.deleted_at.is_not(None))
    base = _apply_filters(base, STO, request)
    total = (await db.execute(select(text('COUNT(*)')).select_from(base.subquery()))).scalar() or 0
    rows = await db.execute(base.offset(offset).limit(limit))
    return {"meta": {"total": total, "page": page, "limit": limit}, "data": [dict(r) for r in rows.mappings().all()]}

@router.post("/sto/restore/{idsto}")
async def restore_sto(idsto: str, db: AsyncSession = Depends(get_db)):
    stmt = update(STO).where(STO.idsto == idsto).values(deleted_at=None)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "restored"}

@router.post("/sto/restore-all")
async def restore_all_sto(db: AsyncSession = Depends(get_db)):
    stmt = update(STO).where(STO.deleted_at.is_not(None)).values(deleted_at=None)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "all restored"}

@router.delete("/sto/purge/{idsto}")
async def purge_sto(idsto: str, db: AsyncSession = Depends(get_db)):
    stmt = delete(STO).where(STO.idsto == idsto)
    await db.execute(stmt)
    await db.commit()
    return {"msg": "purged"}

@router.delete("/sto/purge-all")
async def purge_all_sto(db: AsyncSession = Depends(get_db)):
    stmt = delete(STO).where(STO.deleted_at.is_not(None))
    await db.execute(stmt)
    await db.commit()
    return {"msg": "all purged"}

@router.get("/sto/export")
async def export_sto(request: Request, db: AsyncSession = Depends(get_db)):
    base = select(STO).where(STO.deleted_at.is_(None))
    base = _apply_filters(base, STO, request)
    result = await db.execute(base)
    raw_data = result.mappings().all()
    
    processed_data = []
    for i, d in enumerate(raw_data):
        d_dict = dict(d)
        processed_data.append({
            'NO': i + 1,
            'ID STO': d_dict['idsto'],
            'NAMA STO': d_dict['nama_sto'],
            'WITEL': d_dict['witel'],
            'LONGITUDE': d_dict['longitude'],
            'LATITUDE': d_dict['latitude'],
            'ALAMAT': d_dict['alamat'],
        })

    df = pd.DataFrame(processed_data)
    stream = io.BytesIO()
    writer = pd.ExcelWriter(stream, engine='openpyxl')
    df.to_excel(writer, index=False, sheet_name='STO Data')
    writer.close()
    stream.seek(0)
    
    filename = f'STO-{datetime.now().strftime("%y%m%d-%H%M%S")}.xlsx'
    return StreamingResponse(
        stream,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": f"attachment; filename=\"{filename}\""}
    )

@router.post("/sto/import")
async def import_sto(file_excel: UploadFile = File(...), db: AsyncSession = Depends(get_db)):
    if not file_excel.filename.endswith(('.xlsx', '.xls')):
        raise HTTPException(400, "Format File Tidak Sesuai")
    try:
        content = await file_excel.read()
        df = pd.read_excel(io.BytesIO(content))
        # Expecting columns: idsto, nama_sto, witel, longitude, latitude, alamat
        async with db.begin():
            for _, row in df.iterrows():
                data = {
                    'idsto': str(row.get('idsto', '')).strip(),
                    'nama_sto': str(row.get('nama_sto', '')).strip() if pd.notnull(row.get('nama_sto')) else None,
                    'witel': str(row.get('witel', '')).strip() if pd.notnull(row.get('witel')) else None,
                    'longitude': str(row.get('longitude', '')).strip() if pd.notnull(row.get('longitude')) else None,
                    'latitude': str(row.get('latitude', '')).strip() if pd.notnull(row.get('latitude')) else None,
                    'alamat': str(row.get('alamat', '')).strip() if pd.notnull(row.get('alamat')) else None,
                }
                if not data['idsto']: continue
                # Upsert pattern
                stmt = select(STO).where(STO.idsto == data['idsto'])
                existing = (await db.execute(stmt)).scalar_one_or_none()
                if existing:
                    await db.execute(update(STO).where(STO.idsto == data['idsto']).values(**data))
                else:
                    await db.execute(insert(STO).values(**data))
        return {"status": "success", "message": "Import berhasil"}
    except Exception as e:
        raise HTTPException(500, str(e))
