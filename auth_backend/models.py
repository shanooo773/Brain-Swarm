"""
Pydantic models for authentication and booking system
"""
from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, EmailStr, Field
from enum import Enum


class UserRole(str, Enum):
    USER = "user"
    ADMIN = "admin"


class UserSignUp(BaseModel):
    username: str = Field(..., min_length=3, max_length=50)
    email: EmailStr
    password: str = Field(..., min_length=6)
    full_name: Optional[str] = None


class UserSignIn(BaseModel):
    email: str  # Changed from username to email
    password: str


class UserResponse(BaseModel):
    id: int
    username: str
    email: str
    full_name: Optional[str]
    role: UserRole
    is_active: bool
    created_at: datetime


class TokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    user: UserResponse


class BookingRequest(BaseModel):
    service_type: str = Field(..., description="Type of service to book")
    preferred_date: str = Field(..., description="Preferred booking date")
    message: Optional[str] = None
    phone: Optional[str] = None


class BookingResponse(BaseModel):
    id: int
    user_id: int
    service_type: str
    preferred_date: str
    message: Optional[str]
    phone: Optional[str]
    status: str
    created_at: datetime


class AdminStats(BaseModel):
    total_users: int
    total_bookings: int
    recent_bookings: List[BookingResponse]
    recent_users: List[UserResponse]