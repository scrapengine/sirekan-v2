import asyncio
import json
from fastapi import APIRouter, Depends, HTTPException, BackgroundTasks, Header
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func
from core.database import get_db, SessionLocal
from core.models import WorkerConfig
from pydantic import BaseModel
from core.ws_router import manager
from typing import Optional

router = APIRouter(prefix="/admin/workers", tags=["workers"])

class WorkerConfigUpdate(BaseModel):
    interval_seconds: int
    is_enabled: bool

# Stub worker functions
async def sync_nodeb_task():
    await asyncio.sleep(5)
    return "SUCCESS"

async def assurance_poller_task():
    await asyncio.sleep(3)
    return "SUCCESS"

async def telemetry_sync_task():
    await asyncio.sleep(4)
    return "SUCCESS"

async def run_worker_logic(worker_name: str, user_id: int):
    # Separate session for background task
    async with SessionLocal() as db:
        worker_map = {
            "sync_nodeb": sync_nodeb_task,
            "assurance_poller": assurance_poller_task,
            "telemetry_sync": telemetry_sync_task,
        }
        
        task_func = worker_map.get(worker_name)
        if not task_func:
            return

        try:
            status = await task_func()
            error_msg = None
        except Exception as e:
            status = "FAILED"
            error_msg = str(e)

        # Update DB
        res = await db.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
        worker = res.scalar_one_or_none()
        if worker:
            worker.last_status = status
            worker.last_run_at = func.now()
            worker.error_message = error_msg
            await db.commit()
            
            # Notify Frontend via WebSocket
            await manager.send_personal_message(user_id, {
                "type": "WORKER_UPDATE",
                "payload": {"worker": worker_name, "status": status}
            })

@router.get("")
async def list_workers(db: AsyncSession = Depends(get_db)):
    defaults = ["sync_nodeb", "assurance_poller", "telemetry_sync"]
    for name in defaults:
        res = await db.execute(select(WorkerConfig).where(WorkerConfig.worker_name == name))
        if not res.scalar_one_or_none():
            db.add(WorkerConfig(worker_name=name, interval_seconds=300, is_enabled=True))
    await db.commit()

    result = await db.execute(select(WorkerConfig))
    workers = result.scalars().all()
    return workers

@router.put("/{worker_name}")
async def update_worker(worker_name: str, config: WorkerConfigUpdate, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
    worker = result.scalar_one_or_none()
    if not worker:
        raise HTTPException(status_code=404, detail="Worker not found")
    
    worker.interval_seconds = config.interval_seconds
    worker.is_enabled = config.is_enabled
    await db.commit()
    await db.refresh(worker)
    return worker

@router.post("/{worker_name}/trigger")
async def trigger_worker(
    worker_name: str, 
    background_tasks: BackgroundTasks,
    user_id: int = 1, # Hardcoded for now, normally from auth
    db: AsyncSession = Depends(get_db)
):
    result = await db.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
    worker = result.scalar_one_or_none()
    if not worker:
        raise HTTPException(status_code=404, detail="Worker not found")
    
    if worker.last_status == "RUNNING":
        return {"message": "Worker is already running"}

    worker.last_status = "RUNNING"
    await db.commit()
    
    # Notify Start
    await manager.send_personal_message(user_id, {
        "type": "WORKER_TRIGGERED",
        "payload": {"worker": worker_name, "status": "RUNNING"}
    })
    
    # Run Task in Background
    background_tasks.add_task(run_worker_logic, worker_name, user_id)
    
    return {"message": f"Worker {worker_name} triggered successfully"}
