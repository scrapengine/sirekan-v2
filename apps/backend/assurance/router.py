from fastapi import APIRouter, Depends, HTTPException, Query, UploadFile, File
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from sqlalchemy import or_, and_, func
from typing import List, Optional
from pydantic import BaseModel
from datetime import datetime

from core.database import get_db
from assurance.models import AssuranceTicket

router = APIRouter(prefix="/assurance", tags=["Assurance"])

class AssuranceTicketResponse(BaseModel):
    id: int
    incident: str
    status: Optional[str] = None
    summary: Optional[str] = None
    customer: Optional[str] = None
    witel: Optional[str] = None
    workzone: Optional[str] = None
    reported_date: Optional[str] = None
    status_date: Optional[str] = None
    owner_group: Optional[str] = None
    # Support other fields as needed, for now these are main ones
    service_no: Optional[str] = None
    service_type: Optional[str] = None
    regional: Optional[str] = None
    rca: Optional[str] = None
    resolution: Optional[str] = None

    class Config:
        from_attributes = True

class PaginatedAssuranceResponse(BaseModel):
    data: List[AssuranceTicketResponse]
    total: int
    page: int
    limit: int
    total_pages: int

ACTIVE_STATUSES = ["BACKEND", "ANALYSIS", "DRAFT", "NEW", "PENDING"]

@router.get("/tickets/{incident}", response_model=AssuranceTicketResponse)
async def get_ticket(
    incident: str,
    db: AsyncSession = Depends(get_db)
):
    result = await db.execute(select(AssuranceTicket).filter(AssuranceTicket.incident == incident))
    ticket = result.scalars().first()
    if not ticket:
        raise HTTPException(status_code=404, detail="Ticket not found")
    return ticket

@router.get("/tickets", response_model=PaginatedAssuranceResponse)
async def list_tickets(
    page: int = Query(1, ge=1),
    limit: int = Query(25, ge=-1),
    search: Optional[str] = None,
    status: Optional[str] = None,
    witel: Optional[str] = None,
    workzone: Optional[str] = None,
    is_active: Optional[bool] = None,
    db: AsyncSession = Depends(get_db)
):
    query = select(AssuranceTicket)
    
    conditions = []
    
    if search:
        search_filter = f"%{search}%"
        conditions.append(or_(
            AssuranceTicket.incident.ilike(search_filter),
            AssuranceTicket.summary.ilike(search_filter),
            AssuranceTicket.customer.ilike(search_filter)
        ))
        
    if status:
        conditions.append(AssuranceTicket.status == status)
        
    if witel:
        conditions.append(AssuranceTicket.witel == witel)
        
    if workzone:
        conditions.append(AssuranceTicket.workzone == workzone)
        
    if is_active is True:
        conditions.append(AssuranceTicket.status.in_(ACTIVE_STATUSES))
    elif is_active is False:
        conditions.append(~AssuranceTicket.status.in_(ACTIVE_STATUSES))

    if conditions:
        query = query.filter(and_(*conditions))

    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar() or 0

    # Apply pagination
    if limit != -1:
        skip = (page - 1) * limit
        query = query.offset(skip).limit(limit)

    result = await db.execute(query)
    tickets = result.scalars().all()

    total_pages = (total + limit - 1) // limit if limit > 0 else 1

    return {
        "data": tickets,
        "total": total,
        "page": page,
        "limit": limit,
        "total_pages": total_pages
    }

@router.post("/import-excel")
async def import_excel(file: UploadFile = File(...), db: AsyncSession = Depends(get_db)):
    # Ponytail: Real parser for 80+ columns will be implemented here
    return {"filename": file.filename, "status": "success", "imported": 0}
