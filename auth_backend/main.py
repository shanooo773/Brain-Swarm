"""
FastAPI Authentication and Booking Backend
Separate backend for user authentication, booking system, and admin dashboard
"""
from datetime import datetime
from typing import Optional, List
from contextlib import asynccontextmanager

from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware

try:
    from .models import (
        UserSignUp, UserSignIn, UserResponse, TokenResponse,
        BookingRequest, BookingResponse, AdminStats, UserRole
    )
    from .database import Database
    from .auth import create_access_token, get_user_from_token
except ImportError:
    # Support direct execution
    import sys
    import os
    sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    from auth_backend.models import (
        UserSignUp, UserSignIn, UserResponse, TokenResponse,
        BookingRequest, BookingResponse, AdminStats, UserRole
    )
    from auth_backend.database import Database
    from auth_backend.auth import create_access_token, get_user_from_token


# Initialize database
db = Database()
security = HTTPBearer()


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan manager"""
    # Startup
    print("🚀 Starting Authentication Backend...")
    print("📊 Database initialized")
    yield
    # Shutdown
    print("🛑 Shutting down Authentication Backend...")


# Create FastAPI app
app = FastAPI(
    title="Brain Swarm Auth Backend",
    description="Authentication, Booking and Admin API",
    version="1.0.0",
    lifespan=lifespan
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, specify exact origins
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)) -> dict:
    """Get current authenticated user"""
    user_data = get_user_from_token(credentials.credentials)
    if not user_data:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid authentication credentials",
            headers={"WWW-Authenticate": "Bearer"},
        )
    return user_data


def get_admin_user(current_user: dict = Depends(get_current_user)) -> dict:
    """Ensure current user is admin"""
    if current_user.get("role") != "admin":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required"
        )
    return current_user


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Brain Swarm Authentication Backend",
        "version": "1.0.0",
        "endpoints": {
            "auth": "/auth/",
            "bookings": "/bookings/",
            "admin": "/admin/"
        }
    }


@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "timestamp": datetime.utcnow()}


# Authentication endpoints
@app.post("/auth/signup", response_model=TokenResponse)
async def sign_up(user_data: UserSignUp):
    """User registration"""
    try:
        # Check if user already exists
        existing_user = db.get_user_by_username(user_data.username)
        if existing_user:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Username already registered"
            )
        
        # Create user
        user = db.create_user(
            username=user_data.username,
            email=user_data.email,
            password=user_data.password,
            full_name=user_data.full_name
        )
        
        # Create access token
        token_data = {
            "user_id": user["id"],
            "username": user["username"],
            "role": user["role"]
        }
        access_token = create_access_token(data=token_data)
        
        # Prepare user response
        user_response = UserResponse(
            id=user["id"],
            username=user["username"],
            email=user["email"],
            full_name=user["full_name"],
            role=UserRole(user["role"]),
            is_active=bool(user["is_active"]),
            created_at=datetime.fromisoformat(user["created_at"])
        )
        
        return TokenResponse(access_token=access_token, user=user_response)
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Registration failed: {str(e)}"
        )


@app.post("/auth/signin", response_model=TokenResponse)
async def sign_in(credentials: UserSignIn):
    """User authentication with username or email"""
    user = db.authenticate_user(credentials.username, credentials.password)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid email or password"
        )
    
    if not user["is_active"]:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Account is disabled"
        )
    
    # Create access token
    token_data = {
        "user_id": user["id"],
        "username": user["username"],
        "role": user["role"]
    }
    access_token = create_access_token(data=token_data)
    
    # Prepare user response
    user_response = UserResponse(
        id=user["id"],
        username=user["username"],
        email=user["email"],
        full_name=user["full_name"],
        role=UserRole(user["role"]),
        is_active=bool(user["is_active"]),
        created_at=datetime.fromisoformat(user["created_at"])
    )
    
    return TokenResponse(access_token=access_token, user=user_response)


@app.get("/auth/me", response_model=UserResponse)
async def get_current_user_info(current_user: dict = Depends(get_current_user)):
    """Get current user information"""
    user = db.get_user_by_id(current_user["user_id"])
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found"
        )
    
    return UserResponse(
        id=user["id"],
        username=user["username"],
        email=user["email"],
        full_name=user["full_name"],
        role=UserRole(user["role"]),
        is_active=bool(user["is_active"]),
        created_at=datetime.fromisoformat(user["created_at"])
    )


# Booking endpoints
@app.post("/bookings/", response_model=BookingResponse)
async def create_booking(booking_data: BookingRequest, current_user: dict = Depends(get_current_user)):
    """Create a new booking"""
    booking = db.create_booking(
        user_id=current_user["user_id"],
        service_type=booking_data.service_type,
        preferred_date=booking_data.preferred_date,
        message=booking_data.message,
        phone=booking_data.phone
    )
    
    return BookingResponse(
        id=booking["id"],
        user_id=booking["user_id"],
        service_type=booking["service_type"],
        preferred_date=booking["preferred_date"],
        message=booking["message"],
        phone=booking["phone"],
        status=booking["status"],
        created_at=datetime.fromisoformat(booking["created_at"])
    )


@app.get("/bookings/", response_model=List[BookingResponse])
async def get_user_bookings(current_user: dict = Depends(get_current_user)):
    """Get current user's bookings"""
    bookings = db.get_user_bookings(current_user["user_id"])
    
    return [
        BookingResponse(
            id=booking["id"],
            user_id=booking["user_id"],
            service_type=booking["service_type"],
            preferred_date=booking["preferred_date"],
            message=booking["message"],
            phone=booking["phone"],
            status=booking["status"],
            created_at=datetime.fromisoformat(booking["created_at"])
        )
        for booking in bookings
    ]


# Admin endpoints
@app.get("/admin/stats", response_model=AdminStats)
async def get_admin_dashboard(admin_user: dict = Depends(get_admin_user)):
    """Get admin dashboard statistics"""
    stats = db.get_admin_stats()
    
    # Convert recent bookings
    recent_bookings = [
        BookingResponse(
            id=booking["id"],
            user_id=booking["user_id"],
            service_type=booking["service_type"],
            preferred_date=booking["preferred_date"],
            message=booking["message"],
            phone=booking["phone"],
            status=booking["status"],
            created_at=datetime.fromisoformat(booking["created_at"])
        )
        for booking in stats["recent_bookings"]
    ]
    
    # Convert recent users
    recent_users = [
        UserResponse(
            id=user["id"],
            username=user["username"],
            email=user["email"],
            full_name=user["full_name"],
            role=UserRole(user["role"]),
            is_active=bool(user["is_active"]),
            created_at=datetime.fromisoformat(user["created_at"])
        )
        for user in stats["recent_users"]
    ]
    
    return AdminStats(
        total_users=stats["total_users"],
        total_bookings=stats["total_bookings"],
        recent_bookings=recent_bookings,
        recent_users=recent_users
    )


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)