from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import uvicorn
from core.config import settings

from assurance.router import router as assurance_router
from master_data.router import router as master_data_router
from core.auth_router import router as auth_router
from core.admin_router import router as admin_router
from core.worker_router import router as worker_router
from core.audit_router import router as audit_router
from core.ws_router import router as ws_router
from core.worker_manager import worker_manager

app = FastAPI(title="Sirekan V2 API Monorepo")

@app.on_event("startup")
async def startup_event():
    worker_manager.start()

@app.on_event("shutdown")
async def shutdown_event():
    await worker_manager.stop()

print(f"DATABASE_URL: {settings.DATABASE_URL}")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:5173", "http://127.0.0.1:5173"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(assurance_router)
app.include_router(master_data_router, prefix="/api")
app.include_router(auth_router, prefix="/api")
app.include_router(admin_router, prefix="/api")
app.include_router(worker_router, prefix="/api")
app.include_router(audit_router, prefix="/api")
app.include_router(ws_router, prefix="/api")

@app.get("/")
def read_root():
    return {"status": "online", "system": "Sirekan V2 FastAPI (Monorepo)"}

if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)
