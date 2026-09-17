from pydantic import BaseModel, EmailStr
from typing import Optional
from datetime import datetime

class UserBase(BaseModel):
    username: str
    telegram_id: Optional[str] = None
    role: Optional[str] = "operator"

class UserCreate(UserBase):
    password: str
    otp_code: str

class UserResponse(UserBase):
    id: int
    is_active: bool

    class Config:
        from_attributes = True

class UserLogin(BaseModel):
    username: str
    password: str

class OTPBase(BaseModel):
    telegram_id: str
    code: str
    type: str


class UserOut(BaseModel):
    id: int
    username: str
    email: Optional[str] = None
    telegram_id: Optional[str] = None
    role: str
    is_active: bool
    created_at: datetime

    class Config:
        from_attributes = True


class UserCreateAdmin(BaseModel):
    username: str
    email: Optional[str] = None
    telegram_id: Optional[str] = None
    password: str
    role: str = "operator"
    is_active: bool = True


class UserUpdateAdmin(BaseModel):
    email: Optional[str] = None
    telegram_id: Optional[str] = None
    role: Optional[str] = None
    is_active: Optional[bool] = None
    password: Optional[str] = None
