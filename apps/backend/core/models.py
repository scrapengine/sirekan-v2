from sqlalchemy import Column, Integer, String, Boolean, DateTime, Text, ForeignKey
from sqlalchemy.sql import func
from core.database import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True, nullable=False)
    email = Column(String, unique=True, index=True, nullable=True)
    telegram_id = Column(String, unique=True, index=True, nullable=True)
    hashed_password = Column(Text, nullable=False)
    role = Column(String, default="viewer")
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=func.now())
    updated_at = Column(DateTime, default=func.now(), onupdate=func.now())

class AuditLog(Base):
    __tablename__ = "audit_logs"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    action = Column(String, nullable=False)  # CREATE, UPDATE, DELETE
    target_table = Column(String, nullable=False)
    target_id = Column(Integer, nullable=False)
    # Change tracking fields for diff view
    old_values = Column(Text, nullable=True)  # JSON string of previous state
    new_values = Column(Text, nullable=True)  # JSON string of new state
    ip_address = Column(String, nullable=True)
    created_at = Column(DateTime, default=func.now())

class OTP(Base):
    __tablename__ = "otps"

    id = Column(Integer, primary_key=True, index=True)
    telegram_id = Column(String, nullable=False, index=True)
    otp_code = Column(String(6), nullable=False)
    type = Column(String, nullable=False)  # 'register' or 'reset'
    expires_at = Column(DateTime, nullable=False)
    used = Column(Boolean, default=False, nullable=False)
    created_at = Column(DateTime, default=func.now())

class WorkerConfig(Base):
    __tablename__ = "worker_configs"

    id = Column(Integer, primary_key=True, index=True)
    worker_name = Column(String, unique=True, index=True, nullable=False)
    interval_seconds = Column(Integer, default=300)
    is_enabled = Column(Boolean, default=True)
    last_run_at = Column(DateTime, nullable=True)
    last_status = Column(String, default="IDLE")  # SUCCESS, FAILED, IDLE, RUNNING
    error_message = Column(Text, nullable=True)
    updated_at = Column(DateTime, default=func.now(), onupdate=func.now())
