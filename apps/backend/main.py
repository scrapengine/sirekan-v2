from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import uvicorn

from assurance.router import router as assurance_router
from master_data.router import router as master_data_router

app = FastAPI(title="Sirekan V2 API Monorepo")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(assurance_router)
app.include_router(master_data_router, prefix="/api")

@app.get("/")
def read_root():
    return {"status": "online", "system": "Sirekan V2 FastAPI (Monorepo)"}

if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)
