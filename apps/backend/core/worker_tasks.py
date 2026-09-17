import asyncio
from sqlalchemy.ext.asyncio import AsyncSession
from core.database import get_db
from core.models import WorkerConfig
from core.ws_router import manager

async def sync_nodeb_worker():
    print("Running sync_nodeb_worker...")
    await asyncio.sleep(5) # Simulate work
    print("sync_nodeb_worker finished.")
    return "SUCCESS"

async def assurance_poller_worker():
    print("Running assurance_poller_worker...")
    await asyncio.sleep(3) # Simulate work
    print("assurance_poller_worker finished.")
    return "SUCCESS"

async def telemetry_sync_worker():
    print("Running telemetry_sync_worker...")
    await asyncio.sleep(7) # Simulate work
    print("telemetry_sync_worker finished.")
    return "SUCCESS"

async def run_worker_task(worker_name: str, db_session: AsyncSession, user_id: int):
    worker_func = {
        "sync_nodeb": sync_nodeb_worker,
        "assurance_poller": assurance_poller_worker,
        "telemetry_sync": telemetry_sync_worker,
    }.get(worker_name)

    if not worker_func:
        print(f"Worker function not found for {worker_name}")
        return "FAILED"

    try:
        # Update worker status to RUNNING in DB
        worker_config = await db_session.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
        worker = worker_config.scalar_one_or_none()
        if worker:
            worker.last_status = "RUNNING"
            await db_session.commit()
            await db_session.refresh(worker)
            await manager.send_personal_message(user_id, {
                "type": "WORKER_UPDATE",
                "payload": {"worker": worker_name, "status": "RUNNING"}
            })

        result = await worker_func()
        final_status = result
    except Exception as e:
        print(f"Error running worker {worker_name}: {e}")
        final_status = "FAILED"
    finally:
        # Update worker status in DB
        worker_config = await db_session.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
        worker = worker_config.scalar_one_or_none()
        if worker:
            worker.last_status = final_status
            worker.last_run_at = func.now()
            worker.error_message = str(e) if final_status == "FAILED" else None
            await db_session.commit()
            await db_session.refresh(worker)
            await manager.send_personal_message(user_id, {
                "type": "WORKER_UPDATE",
                "payload": {"worker": worker_name, "status": final_status}
            })

