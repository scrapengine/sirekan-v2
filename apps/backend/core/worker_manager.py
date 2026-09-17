import asyncio
from datetime import datetime, timedelta
from typing import Dict, List, Optional
from sqlalchemy import select, func
from core.database import SessionLocal
from core.models import WorkerConfig
from core.ws_router import manager

# Import the stub worker functions
from core.worker_router import sync_nodeb_task, assurance_poller_task, telemetry_sync_task

class WorkerManager:
    def __init__(self):
        self.running_tasks: Dict[str, asyncio.Task] = {}
        self.main_loop_task: Optional[asyncio.Task] = None
        self.stop_event = asyncio.Event()
        self.worker_functions = {
            "sync_nodeb": sync_nodeb_task,
            "assurance_poller": assurance_poller_task,
            "telemetry_sync": telemetry_sync_task,
        }

    async def _main_worker_loop(self):
        print("WorkerManager: Starting main loop")
        while not self.stop_event.is_set():
            await self._check_and_run_workers()
            try:
                await asyncio.wait_for(self.stop_event.wait(), timeout=10) # Check every 10 seconds
            except asyncio.TimeoutError:
                pass # Continue loop
        print("WorkerManager: Main loop stopped")

    async def _check_and_run_workers(self):
        async with SessionLocal() as db:
            result = await db.execute(select(WorkerConfig))
            configs = result.scalars().all()

            for config in configs:
                if not config.is_enabled:
                    continue

                # Prevent overlapping tasks for the same worker
                if config.worker_name in self.running_tasks and not self.running_tasks[config.worker_name].done():
                    # Task is already running or pending, skip
                    continue
                
                # Check if it's time to run based on interval_seconds
                if self._should_run(config):
                    print(f"WorkerManager: Scheduling {config.worker_name}")
                    task = asyncio.create_task(self._execute_single_worker(config.worker_name)) # Pass worker_name
                    self.running_tasks[config.worker_name] = task

    async def _execute_single_worker(self, worker_name: str): # Receive worker_name
        user_id = 1 # Hardcoded admin user_id for now. Needs proper authentication context in production.
        async with SessionLocal() as db:
            # Fetch latest config state again within the task to handle updates
            result = await db.execute(select(WorkerConfig).where(WorkerConfig.worker_name == worker_name))
            config = result.scalar_one_or_none()
            if not config: # Worker might have been deleted mid-task
                print(f"WorkerManager: Worker config for {worker_name} not found, skipping execution.")
                return

            if config.last_status == "RUNNING": # Double check to prevent unintended overlaps
                print(f"WorkerManager: {worker_name} already running, skipping immediate execution.")
                return

            try:
                # Update status to RUNNING in DB and notify via WS
                config.last_status = "RUNNING"
                config.error_message = None
                await db.commit()
                await manager.send_personal_message(user_id, {
                    "type": "WORKER_UPDATE",
                    "payload": {"worker": worker_name, "status": "RUNNING"}
                })

                # Execute the actual worker function
                worker_func = self.worker_functions.get(worker_name)
                if worker_func:
                    status = await worker_func()
                else:
                    status = "FAILED"
                    raise ValueError(f"Worker function {worker_name} not found")

                config.last_status = status
            except Exception as e:
                config.last_status = "FAILED"
                config.error_message = str(e)
                print(f"WorkerManager: Error in worker {worker_name}: {e}")
            finally:
                config.last_run_at = func.now()
                await db.commit()
                await manager.send_personal_message(user_id, {
                    "type": "WORKER_UPDATE",
                    "payload": {"worker": worker_name, "status": config.last_status, "error_message": config.error_message}
                })

    def _should_run(self, config: WorkerConfig) -> bool:
        if config.last_run_at is None:
            return True
        
        # Use datetime objects for comparison
        now = datetime.now()
        time_since_last_run = (now - config.last_run_at).total_seconds()
        return time_since_last_run >= config.interval_seconds

    def start(self):
        if not self.main_loop_task:
            self.stop_event.clear()
            self.main_loop_task = asyncio.create_task(self._main_worker_loop())
            print("WorkerManager: Started background loop task.")

    async def stop(self):
        if self.main_loop_task:
            print("WorkerManager: Stopping background loop task...")
            self.stop_event.set()
            await self.main_loop_task
            self.main_loop_task = None
            # Optionally, wait for all currently running worker tasks to finish here
            print("WorkerManager: Background loop task stopped.")

worker_manager = WorkerManager()
