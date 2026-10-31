import re
import io
from fastapi import APIRouter, Depends, Query, File, UploadFile, Request, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import text, update, delete, insert
from core.database import get_db
from .service import process_nodeb_record
from pydantic import BaseModel
from typing import Optional, List
from datetime import datetime, date
import pandas as pd
from openpyxl import load_workbook

router = APIRouter(prefix="/master-data")

@router.get("/allnodeb")
async def get_allnodeb(
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    sort_by: Optional[str] = Query("site_id"),
    order: Optional[str] = Query("asc"),
    db: AsyncSession = Depends(get_db)
):
    """
    Get combined NodeB and Radio data for WAN AllNodeb page.
    Includes pagination, search, sorting.
    """
    offset = (page - 1) * limit

    # Parse query params for array support (choice/values filters)
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    # Build NodeB query with joins
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name,
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.type_olt,
        sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
    """
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """

    where_clauses = []
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        db_date_col = "n.on_air"
        where_clauses.append(f"{db_date_col} BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(n.site_id LIKE :search OR n.site_name LIKE :search OR n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search OR n.hostname_metro LIKE :search OR olt.ip_olt LIKE :search OR n.ip_ont LIKE :search OR n.odc LIKE :search OR n.odp LIKE :search OR n.ont_type LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["idsto", "hostname_metro", "hostname_olt", "site_id", "site_name", "port_metro", "port_onu", "hostname_ont", "ip_ont", "ont_type", "serial_number", "odc", "odp", "tikor_site", "on_air"]:
                col = f"n.{c}"
            elif c == "ip_olt":
                col = "olt.ip_olt"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    # Whitelist column mapping for sorting
    sort_mapping = {
        "idnodeb": "n.idnodeb",
        "idsto": "n.idsto",
        "site_id": "n.site_id",
        "site_name": "n.site_name",
        "hostname_metro": "n.hostname_metro",
        "port_metro": "n.port_metro",
        "hostname_olt": "n.hostname_olt",
        "ip_olt": "olt.ip_olt",
        "port_onu": "n.port_onu",
        "hostname_ont": "n.hostname_ont",
        "ip_ont": "n.ip_ont",
        "ont_type": "n.ont_type",
        "serial_number": "n.serial_number",
        "odc": "n.odc",
        "odp": "n.odp",
        "tikor_site": "n.tikor_site",
        "on_air": "n.on_air",
    }

    order_col = sort_mapping.get(sort_by, "n.site_id")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} GROUP BY n.idnodeb ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)
    nodeb_records = [process_nodeb_record(dict(row)) for row in result.mappings().all()]

    # Get existing site_ids from nodeb to avoid duplicates in radio
    nodeb_site_ids = set(r['site_id'] for r in nodeb_records if r.get('site_id'))

    # Get radio data from datanodeb_all
    radio_query = text("""
        SELECT site_id_all, site_name_all, lat_long, transport, tp, sto,
               nsa, kabupaten, kecamatan, kelurahan, tech,
               band_2g, band_3g, band_4g, band_updated, s_power
        FROM datanodeb_all
        WHERE lat_long IS NOT NULL 
        AND lat_long != ''
    """)

    radio_result = await db.execute(radio_query)
    radio_records = radio_result.mappings().all()

    radio_list = []
    for row in radio_records:
        row_dict = dict(row)
        if row_dict.get('site_id_all') in nodeb_site_ids:
            continue
        radio_list.append({
            'site_id': row_dict.get('site_id_all'),
            'site_name': row_dict.get('site_name_all'),
            'transport': row_dict.get('transport'),
            'tp': row_dict.get('tp'),
            'tikor_site': row_dict.get('lat_long'),
            'type': 'radio',
            'nsa': row_dict.get('nsa'),
            'kabupaten': row_dict.get('kabupaten'),
            'kecamatan': row_dict.get('kecamatan'),
            'kelurahan': row_dict.get('kelurahan'),
            'tech': row_dict.get('tech'),
            'band_2g': row_dict.get('band_2g'),
            'band_3g': row_dict.get('band_3g'),
            'band_4g': row_dict.get('band_4g'),
            'band_updated': row_dict.get('band_updated'),
            's_power': row_dict.get('s_power'),
            'sto': row_dict.get('sto'),
        })

    # Combine and apply final sort if needed
    all_data = nodeb_records + radio_list

    return {
        "meta": {"total": total + len(radio_list), "page": page, "limit": limit, "total_pages": ((total + len(radio_list)) + limit - 1) // limit if limit > 0 else 1},
        "data": all_data
    }

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
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    dateParam: Optional[str] = Query("on_air"),
    sort_by: Optional[str] = Query("idnodeb"),
    order: Optional[str] = Query("desc"),
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    
    # Parse query params manually for array support
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name, 
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.type_olt,
        sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
    """
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """
    where_clauses = []
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        db_date_col = "n.on_air" if dateParam == "on_air" else "n.created_at"
        where_clauses.append(f"{db_date_col} BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(n.site_id LIKE :search OR n.site_name LIKE :search OR n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search OR n.hostname_metro LIKE :search OR olt.ip_olt LIKE :search OR n.ip_ont LIKE :search OR n.odc LIKE :search OR n.odp LIKE :search OR n.ont_type LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["idsto", "hostname_metro", "hostname_olt", "site_id", "site_name", "port_metro", "port_onu", "hostname_ont", "ip_ont", "ont_type", "serial_number", "odc", "odp", "tikor_site", "on_air"]:
                col = f"n.{c}"
            elif c == "ip_olt":
                col = "olt.ip_olt"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    # Whitelist column mapping for sorting
    sort_mapping = {
        "idnodeb": "n.idnodeb",
        "idsto": "n.idsto",
        "site_id": "n.site_id",
        "site_name": "n.site_name",
        "hostname_metro": "n.hostname_metro",
        "port_metro": "n.port_metro",
        "hostname_olt": "n.hostname_olt",
        "ip_olt": "olt.ip_olt",
        "port_onu": "n.port_onu",
        "hostname_ont": "n.hostname_ont",
        "ip_ont": "n.ip_ont",
        "ont_type": "n.ont_type",
        "serial_number": "n.serial_number",
        "odc": "n.odc",
        "odp": "n.odp",
        "tikor_site": "n.tikor_site",
        "on_air": "n.on_air",
    }
    
    order_col = sort_mapping.get(sort_by, "n.idnodeb")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} GROUP BY n.idnodeb ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [process_nodeb_record(dict(row)) for row in result.mappings().all()]
    }

@router.get("/maps/nodeb")
async def get_maps_nodeb(
    db: AsyncSession = Depends(get_db)
):
    """
    Return all active NodeB records with coordinates for map display.
    No pagination - returns all records.
    """
    # First get all nodeb records
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name,
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.type_olt,
        sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
    """
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """
    
    # Only filter records with valid coordinates
    where_clauses = ["n.tikor_site IS NOT NULL", "n.tikor_site != ''"]
    where_str = " AND ".join(where_clauses)
    
    data_query = f"SELECT {select_fields} {base_from} AND {where_str} GROUP BY n.idnodeb ORDER BY n.idnodeb DESC"
    
    result = await db.execute(text(data_query))
    records = result.mappings().all()
    
    nodeb_records = [process_nodeb_record(dict(row)) for row in records]
    
    # Get existing site_ids from nodeb to avoid duplicates
    nodeb_site_ids = set(r['site_id'] for r in nodeb_records if r.get('site_id'))
    
    # Now get radio data from datanodeb_all, excluding those already in nodeb
    radio_query = text("""
        SELECT site_id_all, site_name_all, lat_long, transport, tp, sto
        FROM datanodeb_all
        WHERE lat_long IS NOT NULL 
        AND lat_long != ''
    """)
    # Verified columns against datanodeb_all schema via PRAGMA table_info.
    
    radio_result = await db.execute(radio_query)
    radio_records = radio_result.mappings().all()
    
    radio_list = []
    for row in radio_records:
        row_dict = dict(row)
        if row_dict.get('site_id_all') in nodeb_site_ids:
            continue  # Skip if already exists in nodeb
        radio_list.append({
            'site_id': row_dict.get('site_id_all'),
            'site_name': row_dict.get('site_name_all'),
            'transport': row_dict.get('transport'),
            'tp': row_dict.get('tp'),
            'tikor_site': row_dict.get('lat_long'),
            'type': 'radio'
        })
    
    return {
        "nodeb": nodeb_records,
        "radio": radio_list
    }


@router.get("/nodeb/trash")
async def get_trash_nodeb(
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    sort_by: Optional[str] = Query("deleted_at"),
    order: Optional[str] = Query("desc"),
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    
    # Parse query params manually for array support
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

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
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NOT NULL
    """
    where_clauses = []
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        db_date_col = "n.deleted_at"
        where_clauses.append(f"{db_date_col} BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(n.site_id LIKE :search OR n.site_name LIKE :search OR n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search OR n.hostname_metro LIKE :search OR olt.ip_olt LIKE :search OR n.ip_ont LIKE :search OR n.odc LIKE :search OR n.odp LIKE :search OR n.ont_type LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["idsto", "hostname_metro", "hostname_olt", "site_id", "site_name", "port_metro", "port_onu", "hostname_ont", "ip_ont", "ont_type", "serial_number", "odc", "odp", "tikor_site", "on_air"]:
                col = f"n.{c}"
            elif c == "ip_olt":
                col = "olt.ip_olt"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")
    
    # Whitelist column mapping for sorting
    sort_mapping = {
        "idnodeb": "n.idnodeb",
        "deleted_at": "n.deleted_at",
        "idsto": "n.idsto",
        "site_id": "n.site_id",
        "site_name": "n.site_name",
        "hostname_metro": "n.hostname_metro",
        "port_metro": "n.port_metro",
        "hostname_olt": "n.hostname_olt",
        "ip_olt": "olt.ip_olt",
        "port_onu": "n.port_onu",
        "hostname_ont": "n.hostname_ont",
        "ip_ont": "n.ip_ont",
        "ont_type": "n.ont_type",
        "serial_number": "n.serial_number",
        "odc": "n.odc",
        "odp": "n.odp",
        "tikor_site": "n.tikor_site",
        "on_air": "n.on_air",
    }
    
    order_col = sort_mapping.get(sort_by, "n.deleted_at")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} GROUP BY n.idnodeb ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [process_nodeb_record(dict(row)) for row in result.mappings().all()]
    }


from fastapi.responses import StreamingResponse
import io
import pandas as pd
import openpyxl.styles
import openpyxl.utils
from datetime import datetime, date

@router.get("/nodeb/export")
async def export_nodeb(
    request: Request,
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    dateParam: Optional[str] = Query("on_air"),
    db: AsyncSession = Depends(get_db)
):
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name, 
        n.hostname_metro AS hostname_metro_nodeb, m.ip_metro, 
        olt.port_metro AS port_metro_olt, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, olt.ip_olt, 
        n.hostname_ont, n.ip_ont, n.port_onu, n.ont_type, 
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air
    """
    from_clause = """
        FROM datanodeb n
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """
    
    where_clauses = []
    params = {}

    if fromdate and untildate:
        # Map frontend field names to DB column names if necessary
        db_date_col = "n.on_air" if dateParam == "on_air" else "n.created_at"
        where_clauses.append(f"{db_date_col} BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            # Handle table prefixes for ambiguity
            col = c
            if c in ["idsto", "hostname_metro", "hostname_olt"]: col = f"n.{c}"
            elif c == "ip_olt": col = "olt.ip_olt"
            
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    where_str = ""
    if where_clauses:
        where_str = " AND " + " AND ".join(where_clauses)

    query = text(f"SELECT {select_fields} {from_clause} {where_str}")
    result = await db.execute(query, params)
    raw_data = result.mappings().all()

    processed_data = []
    for i, d in enumerate(raw_data):
        d_dict = dict(d)
        
        port_metro_val = d_dict['port_metro_nodeb']
        if d_dict['port_metro_olt'] is not None:
            port_metro_val = d_dict['port_metro_olt']

        processed_data.append({
            'NO': i + 1,
            'STO': d_dict['idsto_nodeb'],
            'SITE_ID': d_dict['site_id'],
            'SITE_NAME': d_dict['site_name'],
            'HOSTNAME_METRO': d_dict['hostname_metro_nodeb'],
            'IP_METRO': d_dict['ip_metro'],
            'PORT_METRO': port_metro_val,
            'HOSTNAME_OLT': d_dict['hostname_olt_nodeb'],
            'IP_OLT': d_dict['ip_olt'],
            'PORT_ONU': d_dict['port_onu'],
            'HOSTNAME_ONT': d_dict['hostname_ont'],
            'IP_ONT': d_dict['ip_ont'],
            'ONT_TYPE': d_dict['ont_type'],
            'SERIAL_NUMBER': d_dict['serial_number'],
            'ODC': d_dict['odc'],
            'ODP': d_dict['odp'],
            'COORDINATE': d_dict['tikor_site'],
            'ON AIR': d_dict['on_air'],
        })

    df = pd.DataFrame(processed_data)
    stream = io.BytesIO()

    writer = pd.ExcelWriter(stream, engine='openpyxl')
    df.to_excel(writer, index=False, sheet_name='NodeB Data')
    
    workbook = writer.book
    worksheet = writer.sheets['NodeB Data']

    header_fill = openpyxl.styles.PatternFill(start_color="499ff2", end_color="499ff2", fill_type="solid")
    header_font = openpyxl.styles.Font(bold=True, color="FFFFFF")
    
    thin_border = openpyxl.styles.Border(left=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        right=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        top=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        bottom=openpyxl.styles.Side(style='thin', color='FF000000'))
    
    for col_idx in range(1, len(df.columns) + 1):
        cell = worksheet.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        worksheet.column_dimensions[openpyxl.utils.get_column_letter(col_idx)].auto_size = True
        
    for row_idx in range(1, len(df) + 2):
        for col_idx in range(1, len(df.columns) + 1):
            cell = worksheet.cell(row=row_idx, column=col_idx)
            cell.border = thin_border

    writer.close()
    stream.seek(0)
    
    filename = f'NODEB-{datetime.now().strftime("%y%m%d-%H%M%S")}.xlsx'
    
    return StreamingResponse(
        stream,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": f"attachment; filename=\"{filename}\""}
    )

@router.post("/nodeb/import")
async def import_nodeb(file_excel: UploadFile = File(...), db: AsyncSession = Depends(get_db)):
    if not file_excel.filename.endswith(('.xlsx', '.xls')):
        raise HTTPException(status_code=400, detail="Format File Tidak Sesuai")
        
    try:
        content = await file_excel.read()
        df = pd.read_excel(io.BytesIO(content), header=None)
        
        async with db.begin():
            for i, row in df.iterrows():
                if i == 0: continue
                
                # Extract values (assuming 0-indexed per mapping)
                val = row.tolist()
                
                # Data mapping based on legacy CI4
                data_nodeb_insert = {
                    'idsto': val[0],
                    'site_id': val[1],
                    'site_name': val[2],
                    'hostname_metro': val[3],
                    'port_metro': val[4],
                    'hostname_olt': val[5],
                    'port_onu': val[6],
                    'hostname_ont': val[7],
                    'ip_ont': val[8],
                    'ont_type': val[9],
                    'serial_number': val[10],
                    'odc': val[11],
                    'odp': val[12],
                    'tikor_site': val[13],
                    'on_air': datetime.strptime(str(val[14]), '%Y-%m-%d %H:%M:%S').date() if pd.notnull(val[14]) else None
                }
                
                res = await db.execute(insert(text("datanodeb")).values(**data_nodeb_insert))
                idnodeb = res.inserted_primary_key[0]
                
                # Cacti
                if pd.notnull(val[15]):
                    await db.execute(text("INSERT INTO cacti (graph_id, idnodeb) VALUES (:g, :id)"), {"g": val[15], "id": idnodeb})
                    
                # ONT & ONT Type
                serial = str(val[10]).strip()
                ont_raw = str(val[9]).strip()
                if serial and serial.upper() != 'TO IPASO' and ont_raw:
                    merk = ont_raw
                    typ = None
                    if '-' in ont_raw:
                        merk, typ = [x.strip() for x in ont_raw.split('-', 1)]
                    
                    # ONT Type
                    exists_type = (await db.execute(text("SELECT id FROM ont_type WHERE merk = :m AND type = :t"), {"m": merk, "t": typ})).scalar()
                    if not exists_type:
                        await db.execute(text("INSERT INTO ont_type (merk, type) VALUES (:m, :t)"), {"m": merk, "t": typ})
                    
                    # Data ONT
                    tgl = datetime.strptime(str(val[14]), '%Y-%m-%d %H:%M:%S').date() if pd.notnull(val[14]) else None
                    ont = (await db.execute(text("SELECT idont, merk, type, installed, desc FROM dataont WHERE serial_number = :s"), {"s": serial})).mappings().first()
                    
                    if not ont:
                        await db.execute(text("INSERT INTO dataont (merk, type, serial_number, status, idsto, installed, desc) VALUES (:m, :t, :s, 'BAIK', :sto, :ins, :d)"), 
                                        {"m": merk, "t": typ, "s": serial, "sto": val[0], "ins": tgl, "d": f"{tgl} Installed to {val[1]}" if tgl else f"Installed to {val[1]}"})
                    else:
                        if val[1] not in ont['desc']:
                            new_desc = ont['desc'] + f"\n{tgl} Cascade to {val[1]}" if tgl else ont['desc'] + f"\nInstalled to {val[1]}"
                            await db.execute(text("UPDATE dataont SET desc = :d WHERE idont = :id"), {"d": new_desc, "id": ont['idont']})
                            
                # datanodeb_all
                site_id_all = val[1]
                site_name_all = re.sub(r'\s-\s(ONT|LINK)\s\d+$', '', str(val[2]), flags=re.IGNORECASE)
                exists_all = (await db.execute(text("SELECT id FROM datanodeb_all WHERE site_id_all = :s"), {"s": site_id_all})).scalar()
                
                if exists_all:
                    await db.execute(text("UPDATE datanodeb_all SET transport = 'Metro-E Telkom' WHERE site_id_all = :s"), {"s": site_id_all})
                else:
                    await db.execute(text("INSERT INTO datanodeb_all (site_id_all, site_name_all, lat_long, sto, transport) VALUES (:s, :n, :l, :st, 'Metro-E Telkom')"),
                                    {"s": site_id_all, "n": site_name_all, "l": val[13], "st": val[0]})
                                    
        return {"status": "success", "message": "Import berhasil"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

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


@router.get("/allnodeb")
async def get_allnodeb(
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    sort_by: Optional[str] = Query("site_id"),
    order: Optional[str] = Query("asc"),
    db: AsyncSession = Depends(get_db)
):
    """
    Get combined NodeB and Radio data for WAN AllNodeb page.
    Includes pagination, search, sorting.
    """
    offset = (page - 1) * limit

    # Parse query params for array support (choice/values filters)
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    # Build NodeB query with joins
    select_fields = """
        n.idnodeb, n.idsto AS idsto_nodeb, n.site_id, n.site_name,
        n.hostname_metro AS hostname_metro_nodeb, n.port_metro AS port_metro_nodeb,
        n.hostname_olt AS hostname_olt_nodeb, n.port_onu, n.ont_type,
        n.serial_number, n.odc, n.odp, n.tikor_site, n.on_air, n.hostname_ont, n.ip_ont,
        c.graph_id, ont.merk, ont.type,
        olt.port_metro AS port_metro_olt, olt.type_olt,
        sto.idsto, m.ip_metro, m.hostname_metro, olt.ip_olt
    """
    base_from = """
        FROM datanodeb n
        LEFT JOIN cacti c ON c.idnodeb = n.idnodeb
        LEFT JOIN dataont ont ON ont.serial_number = n.serial_number
        LEFT JOIN datasto sto ON sto.idsto = n.idsto
        LEFT JOIN datametro m ON m.hostname_metro = n.hostname_metro
        LEFT JOIN dataolt olt ON olt.hostname_olt = n.hostname_olt
        WHERE n.deleted_at IS NULL
    """

    where_clauses = []
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        db_date_col = "n.on_air"
        where_clauses.append(f"{db_date_col} BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(n.site_id LIKE :search OR n.site_name LIKE :search OR n.hostname_olt LIKE :search OR n.hostname_ont LIKE :search OR n.serial_number LIKE :search OR n.hostname_metro LIKE :search OR olt.ip_olt LIKE :search OR n.ip_ont LIKE :search OR n.odc LIKE :search OR n.odp LIKE :search OR n.ont_type LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["idsto", "hostname_metro", "hostname_olt", "site_id", "site_name", "port_metro", "port_onu", "hostname_ont", "ip_ont", "ont_type", "serial_number", "odc", "odp", "tikor_site", "on_air"]:
                col = f"n.{c}"
            elif c == "ip_olt":
                col = "olt.ip_olt"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    # Whitelist column mapping for sorting
    sort_mapping = {
        "idnodeb": "n.idnodeb",
        "idsto": "n.idsto",
        "site_id": "n.site_id",
        "site_name": "n.site_name",
        "hostname_metro": "n.hostname_metro",
        "port_metro": "n.port_metro",
        "hostname_olt": "n.hostname_olt",
        "ip_olt": "olt.ip_olt",
        "port_onu": "n.port_onu",
        "hostname_ont": "n.hostname_ont",
        "ip_ont": "n.ip_ont",
        "ont_type": "n.ont_type",
        "serial_number": "n.serial_number",
        "odc": "n.odc",
        "odp": "n.odp",
        "tikor_site": "n.tikor_site",
        "on_air": "n.on_air",
    }

    order_col = sort_mapping.get(sort_by, "n.site_id")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT n.idnodeb) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} GROUP BY n.idnodeb ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)
    nodeb_records = [process_nodeb_record(dict(row)) for row in result.mappings().all()]

    # Get existing site_ids from nodeb to avoid duplicates in radio
    nodeb_site_ids = set(r['site_id'] for r in nodeb_records if r.get('site_id'))

    # Get radio data from datanodeb_all
    radio_query = text("""
        SELECT site_id_all, site_name_all, lat_long, transport, tp, sto,
               nsa, kabupaten, kecamatan, kelurahan, tech,
               band_2g, band_3g, band_4g, band_updated, s_power
        FROM datanodeb_all
        WHERE lat_long IS NOT NULL 
        AND lat_long != ''
    """)

    radio_result = await db.execute(radio_query)
    radio_records = radio_result.mappings().all()

    radio_list = []
    for row in radio_records:
        row_dict = dict(row)
        if row_dict.get('site_id_all') in nodeb_site_ids:
            continue
        radio_list.append({
            'site_id': row_dict.get('site_id_all'),
            'site_name': row_dict.get('site_name_all'),
            'transport': row_dict.get('transport'),
            'tp': row_dict.get('tp'),
            'tikor_site': row_dict.get('lat_long'),
            'type': 'radio',
            'nsa': row_dict.get('nsa'),
            'kabupaten': row_dict.get('kabupaten'),
            'kecamatan': row_dict.get('kecamatan'),
            'kelurahan': row_dict.get('kelurahan'),
            'tech': row_dict.get('tech'),
            'band_2g': row_dict.get('band_2g'),
            'band_3g': row_dict.get('band_3g'),
            'band_4g': row_dict.get('band_4g'),
            'band_updated': row_dict.get('band_updated'),
            's_power': row_dict.get('s_power'),
            'sto': row_dict.get('sto'),
        })

    # Combine and apply final sort if needed
    all_data = nodeb_records + radio_list

    return {
        "meta": {"total": total + len(radio_list), "page": page, "limit": limit, "total_pages": ((total + len(radio_list)) + limit - 1) // limit if limit > 0 else 1},
        "data": all_data
    }

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
async def get_sto(
    request: Request,
    page: int = 1,
    limit: int = 25,
    keyword: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    where_clauses = ["deleted_at IS NULL", "idsto != '-'"]
    params = {"limit": limit, "offset": offset}

    if keyword:
        where_clauses.append("(idsto LIKE :keyword OR nama_sto LIKE :keyword OR longitude LIKE :keyword OR latitude LIKE :keyword OR alamat LIKE :keyword)")
        params["keyword"] = f"%{keyword}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v: continue
            p_key = f"val_{i}"
            choice_clauses.append(f"{c} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    where_str = " WHERE " + " AND ".join(where_clauses)
    count_query = f"SELECT COUNT(*) FROM datasto {where_str}"
    data_query = f"SELECT * FROM datasto {where_str} ORDER BY idsto ASC LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [dict(row) for row in result.mappings().all()]
    }

@router.post("/sto")
async def create_sto(data: dict, db: AsyncSession = Depends(get_db)):
    keys = ", ".join(data.keys())
    values = ", ".join([f":{k}" for k in data.keys()])
    await db.execute(text(f"INSERT INTO datasto ({keys}) VALUES ({values})"), data)
    await db.commit()
    return {"status": "success"}

@router.get("/sto/{id}")
async def get_sto_detail(id: str, db: AsyncSession = Depends(get_db)):
    result = await db.execute(text("SELECT * FROM datasto WHERE idsto = :id"), {"id": id})
    row = result.mappings().first()
    if not row: raise HTTPException(status_code=404, detail="STO not found")
    return dict(row)

@router.put("/sto/{id}")
async def update_sto(id: str, data: dict, db: AsyncSession = Depends(get_db)):
    set_clause = ", ".join([f"{k} = :{k}" for k in data.keys()])
    params = {**data, "id": id}
    await db.execute(text(f"UPDATE datasto SET {set_clause} WHERE idsto = :id"), params)
    await db.commit()
    return {"status": "success"}

@router.delete("/sto/{id}")
async def delete_sto(id: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datasto SET deleted_at = CURRENT_TIMESTAMP WHERE idsto = :id"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.get("/sto-trash")
async def get_sto_trash(
    request: Request,
    page: int = 1,
    limit: int = 25,
    keyword: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    where_clauses = ["deleted_at IS NOT NULL"]
    params = {"limit": limit, "offset": offset}
    if keyword:
        where_clauses.append("(idsto LIKE :keyword OR nama_sto LIKE :keyword)")
        params["keyword"] = f"%{keyword}%"
    
    where_str = " WHERE " + " AND ".join(where_clauses)
    count_query = f"SELECT COUNT(*) FROM datasto {where_str}"
    data_query = f"SELECT * FROM datasto {where_str} ORDER BY deleted_at DESC LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit},
        "data": [dict(row) for row in result.mappings().all()]
    }

@router.post("/sto/restore/{id}")
async def restore_sto(id: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datasto SET deleted_at = NULL WHERE idsto = :id"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.delete("/sto/purge/{id}")
async def purge_sto(id: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("DELETE FROM datasto WHERE idsto = :id AND deleted_at IS NOT NULL"), {"id": id})
    await db.commit()
    return {"status": "success"}

@router.get("/metro")
async def get_metro(
    request: Request,
    page: int = 1,
    limit: int = 25,
    keyword: str = "",
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    where_clauses = ["deleted_at IS NULL", "hostname_metro != '-'"]
    params = {"limit": limit, "offset": offset}

    if keyword:
        where_clauses.append("(hostname_metro LIKE :keyword OR ip_metro LIKE :keyword OR idsto LIKE :keyword)")
        params["keyword"] = f"%{keyword}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v: continue
            p_key = f"val_{i}"
            choice_clauses.append(f"{c} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    where_str = " WHERE " + " AND ".join(where_clauses)
    count_query = f"SELECT COUNT(*) FROM datametro {where_str}"
    data_query = f"SELECT * FROM datametro {where_str} ORDER BY hostname_metro ASC LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [dict(row) for row in result.mappings().all()]
    }

@router.post("/metro")
async def create_metro(data: dict, db: AsyncSession = Depends(get_db)):
    keys = ", ".join(data.keys())
    values = ", ".join([f":{k}" for k in data.keys()])
    await db.execute(text(f"INSERT INTO datametro ({keys}) VALUES ({values})"), data)
    await db.commit()
    return {"status": "success"}

@router.put("/metro/{hostname}")
async def update_metro(hostname: str, data: dict, db: AsyncSession = Depends(get_db)):
    set_clause = ", ".join([f"{k} = :{k}" for k in data.keys()])
    params = {**data, "hostname": hostname}
    await db.execute(text(f"UPDATE datametro SET {set_clause} WHERE hostname_metro = :hostname"), params)
    await db.commit()
    return {"status": "success"}

@router.delete("/metro/{hostname}")
async def delete_metro(hostname: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE datametro SET deleted_at = CURRENT_TIMESTAMP WHERE hostname_metro = :hostname"), {"hostname": hostname})
    await db.commit()
    return {"status": "success"}

@router.get("/olt")
async def list_olt(
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    sort_by: Optional[str] = Query("hostname_olt"),
    order: Optional[str] = Query("asc"),
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit

    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    select_fields = """
        o.hostname_olt, o.ip_olt, o.port_olt, o.platform, o.type_olt, o.witel,
        o.hostname_metro, o.port_metro, o.idsto,
        m.ip_metro,
        o.deleted_at
    """
    base_from = """
        FROM dataolt o
        LEFT JOIN datametro m ON m.hostname_metro = o.hostname_metro
        WHERE o.hostname_olt != '-'
    """

    where_clauses = ["o.deleted_at IS NULL"]
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        where_clauses.append("o.created_at BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(o.hostname_olt LIKE :search OR o.ip_olt LIKE :search OR o.platform LIKE :search OR o.type_olt LIKE :search OR o.witel LIKE :search OR o.hostname_metro LIKE :search OR o.port_metro LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["witel", "idsto", "port_metro", "ip_olt", "hostname_olt", "port_olt", "platform", "hostname_metro", "type_olt"]:
                col = f"o.{c}"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    sort_mapping = {
        "hostname_olt": "o.hostname_olt",
        "ip_olt": "o.ip_olt",
        "port_olt": "o.port_olt",
        "platform": "o.platform",
        "type_olt": "o.type_olt",
        "witel": "o.witel",
        "hostname_metro": "o.hostname_metro",
        "port_metro": "o.port_metro",
        "idsto": "o.idsto",
        "ip_metro": "m.ip_metro",
    }

    order_col = sort_mapping.get(sort_by, "o.hostname_olt")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT o.hostname_olt) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [dict(row) for row in result.mappings().all()]
    }


class OLTUpdate(BaseModel):
    witel: Optional[str] = None
    idsto: Optional[str] = None
    port_metro: Optional[str] = None
    ip_olt: Optional[str] = None
    hostname_olt: str
    port_olt: Optional[str] = None
    platform: Optional[str] = None
    hostname_metro: Optional[str] = None
    type_olt: Optional[str] = None


@router.get("/olt/trash")
async def get_trash_olt(
    request: Request,
    page: int = 1,
    limit: int = 25,
    search: str = "",
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    sort_by: Optional[str] = Query("deleted_at"),
    order: Optional[str] = Query("desc"),
    db: AsyncSession = Depends(get_db)
):
    offset = (page - 1) * limit

    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    select_fields = """
        o.hostname_olt, o.ip_olt, o.port_olt, o.platform, o.type_olt, o.witel,
        o.hostname_metro, o.port_metro, o.idsto,
        m.ip_metro,
        o.deleted_at
    """
    base_from = """
        FROM dataolt o
        LEFT JOIN datametro m ON m.hostname_metro = o.hostname_metro
        WHERE o.hostname_olt != '-' AND o.deleted_at IS NOT NULL
    """

    where_clauses = []
    params = {"limit": limit, "offset": offset}

    if fromdate and untildate:
        where_clauses.append("o.deleted_at BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if search:
        where_clauses.append("(o.hostname_olt LIKE :search OR o.ip_olt LIKE :search OR o.platform LIKE :search OR o.type_olt LIKE :search OR o.witel LIKE :search OR o.hostname_metro LIKE :search OR o.port_metro LIKE :search)")
        params["search"] = f"%{search}%"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["witel", "idsto", "port_metro", "ip_olt", "hostname_olt", "port_olt", "platform", "hostname_metro", "type_olt"]:
                col = f"o.{c}"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    sort_mapping = {
        "hostname_olt": "o.hostname_olt",
        "deleted_at": "o.deleted_at",
        "ip_olt": "o.ip_olt",
        "port_olt": "o.port_olt",
        "platform": "o.platform",
        "type_olt": "o.type_olt",
        "witel": "o.witel",
        "hostname_metro": "o.hostname_metro",
        "port_metro": "o.port_metro",
        "idsto": "o.idsto",
        "ip_metro": "m.ip_metro",
    }

    order_col = sort_mapping.get(sort_by, "o.deleted_at")
    order_dir = "ASC" if order.lower() == "asc" else "DESC"

    where_str = " AND " + " AND ".join(where_clauses) if where_clauses else ""
    count_query = f"SELECT COUNT(DISTINCT o.hostname_olt) {base_from} {where_str}"
    data_query = f"SELECT {select_fields} {base_from} {where_str} ORDER BY {order_col} {order_dir} LIMIT :limit OFFSET :offset"

    total = (await db.execute(text(count_query), params)).scalar() or 0
    result = await db.execute(text(data_query), params)

    return {
        "meta": {"total": total, "page": page, "limit": limit, "total_pages": (total + limit - 1) // limit if limit > 0 else 1},
        "data": [dict(row) for row in result.mappings().all()]
    }


@router.get("/olt/{hostname_olt}")
async def get_olt_detail(hostname_olt: str, db: AsyncSession = Depends(get_db)):
    query = text("""
        SELECT 
            o.hostname_olt, o.ip_olt, o.port_olt, o.platform, o.type_olt, o.witel,
            o.hostname_metro, o.port_metro, o.idsto,
            m.ip_metro,
            o.created_at, o.updated_at, o.deleted_at
        FROM dataolt o
        LEFT JOIN datametro m ON m.hostname_metro = o.hostname_metro
        WHERE o.hostname_olt = :hostname_olt
    """)
    result = await db.execute(query, {"hostname_olt": hostname_olt})
    row = result.mappings().first()
    if not row:
        return {"error": "Not found"}
    return dict(row)


@router.post("/olt")
async def create_olt(data: OLTUpdate, db: AsyncSession = Depends(get_db)):
    insert_data = data.dict()
    stmt = insert(text("dataolt")).values(**insert_data)
    await db.execute(stmt)
    await db.commit()
    return {"status": "success", "hostname_olt": data.hostname_olt}


@router.patch("/olt/{hostname_olt}")
async def update_olt(hostname_olt: str, data: OLTUpdate, db: AsyncSession = Depends(get_db)):
    update_data = data.dict(exclude={"hostname_olt"})
    stmt = update(text("dataolt")).where(text("hostname_olt = :hostname_olt")).values(**update_data)
    await db.execute(stmt, {"hostname_olt": hostname_olt})
    await db.commit()
    return {"status": "success"}


@router.post("/olt/restore/{hostname_olt}")
async def restore_olt(hostname_olt: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE dataolt SET deleted_at = NULL WHERE hostname_olt = :hostname_olt"), {"hostname_olt": hostname_olt})
    await db.commit()
    return {"status": "success"}


@router.post("/olt/restore-all")
async def restore_all_olt(db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE dataolt SET deleted_at = NULL WHERE deleted_at IS NOT NULL"))
    await db.commit()
    return {"status": "success"}


@router.delete("/olt/{hostname_olt}")
async def delete_olt(hostname_olt: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("UPDATE dataolt SET deleted_at = datetime('now') WHERE hostname_olt = :hostname_olt"), {"hostname_olt": hostname_olt})
    await db.commit()
    return {"status": "success"}


@router.delete("/olt/purge/{hostname_olt}")
async def purge_olt(hostname_olt: str, db: AsyncSession = Depends(get_db)):
    await db.execute(text("DELETE FROM dataolt WHERE hostname_olt = :hostname_olt"), {"hostname_olt": hostname_olt})
    await db.commit()
    return {"status": "success"}


@router.delete("/olt/purge-all")
async def purge_all_olt(db: AsyncSession = Depends(get_db)):
    await db.execute(text("DELETE FROM dataolt WHERE deleted_at IS NOT NULL"))
    await db.commit()
    return {"status": "success"}


@router.get("/olt/export")
async def export_olt(
    request: Request,
    fromdate: Optional[str] = Query(None),
    untildate: Optional[str] = Query(None),
    db: AsyncSession = Depends(get_db)
):
    choice = request.query_params.getlist('choice[]') or request.query_params.getlist('choice')
    values = request.query_params.getlist('values[]') or request.query_params.getlist('values')

    select_fields = """
        o.hostname_olt, o.ip_olt, o.port_olt, o.platform, o.type_olt, o.witel,
        o.hostname_metro, m.ip_metro, o.port_metro, o.idsto
    """
    from_clause = """
        FROM dataolt o
        LEFT JOIN datametro m ON m.hostname_metro = o.hostname_metro
        WHERE o.hostname_olt != '-' AND o.deleted_at IS NULL
    """

    where_clauses = []
    params = {}

    if fromdate and untildate:
        where_clauses.append("o.created_at BETWEEN :fromdate AND :untildate")
        params["fromdate"] = f"{fromdate} 00:00:00"
        params["untildate"] = f"{untildate} 23:59:59"

    if choice and values:
        choice_clauses = []
        for i, (c, v) in enumerate(zip(choice, values)):
            if not v or c == "cancel": continue
            col = c
            if c in ["witel", "idsto", "port_metro", "ip_olt", "hostname_olt", "port_olt", "platform", "hostname_metro", "type_olt"]:
                col = f"o.{c}"
            elif c == "ip_metro":
                col = "m.ip_metro"
            p_key = f"val_{i}"
            choice_clauses.append(f"{col} LIKE :{p_key}")
            params[p_key] = f"%{v}%"
        if choice_clauses:
            where_clauses.append("(" + " OR ".join(choice_clauses) + ")")

    where_str = ""
    if where_clauses:
        where_str = " AND " + " AND ".join(where_clauses)

    query = text(f"SELECT {select_fields} {from_clause} {where_str}")
    result = await db.execute(query, params)
    raw_data = result.mappings().all()

    processed_data = []
    for i, d in enumerate(raw_data):
        d_dict = dict(d)
        processed_data.append({
            'NO': i + 1,
            'WITEL': d_dict.get('witel'),
            'STO': d_dict.get('idsto'),
            'HOSTNAME_METRO': d_dict.get('hostname_metro'),
            'IP_METRO': d_dict.get('ip_metro'),
            'PORT_METRO': d_dict.get('port_metro'),
            'HOSTNAME_OLT': d_dict.get('hostname_olt'),
            'IP_OLT': d_dict.get('ip_olt'),
            'PORT_OLT': d_dict.get('port_olt'),
            'PLATFORM': d_dict.get('platform'),
            'TYPE_OLT': d_dict.get('type_olt'),
        })

    df = pd.DataFrame(processed_data)
    stream = io.BytesIO()

    writer = pd.ExcelWriter(stream, engine='openpyxl')
    df.to_excel(writer, index=False, sheet_name='OLT Data')

    workbook = writer.book
    worksheet = writer.sheets['OLT Data']

    header_fill = openpyxl.styles.PatternFill(start_color="499ff2", end_color="499ff2", fill_type="solid")
    header_font = openpyxl.styles.Font(bold=True, color="FFFFFF")

    thin_border = openpyxl.styles.Border(left=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        right=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        top=openpyxl.styles.Side(style='thin', color='FF000000'),
                                        bottom=openpyxl.styles.Side(style='thin', color='FF000000'))

    for col_idx in range(1, len(df.columns) + 1):
        cell = worksheet.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        worksheet.column_dimensions[openpyxl.utils.get_column_letter(col_idx)].auto_size = True

    for row_idx in range(1, len(df) + 2):
        for col_idx in range(1, len(df.columns) + 1):
            cell = worksheet.cell(row=row_idx, column=col_idx)
            cell.border = thin_border

    writer.close()
    stream.seek(0)

    filename = f'OLT-{datetime.now().strftime("%y%m%d-%H%M%S")}.xlsx'

    return StreamingResponse(
        stream,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": f'attachment; filename="{filename}"'}
    )
