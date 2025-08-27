# Brain-Swarm - Educational Platform with Authentication System

A modern educational platform featuring a Django frontend with FastAPI authentication backend.

## 🚀 Quick Start

**Option 1: Use the setup script (Recommended)**
```bash
# Install dependencies
pip install -r requirements.txt

# Start both services (FastAPI backend + Django frontend)
./start_services.sh
```

**Option 2: Manual startup**
```bash
# Install dependencies
pip install -r requirements.txt

# Terminal 1 - Start FastAPI backend
cd auth_backend
python main.py

# Terminal 2 - Start Django frontend  
python manage.py runserver 8000
```

## 🔐 Authentication System

The platform uses email-based authentication with JWT tokens:

### Demo Accounts
- **Demo User**: `demo@brainswarm.com` / `demo123`
- **Demo Admin**: `demoadmin@brainswarm.com` / `demoadmin123`
- **Admin**: `admin@brainswarm.com` / `admin123`

### Features
- ✅ Email/password login (not username)
- ✅ JWT token-based authentication
- ✅ Demo accounts with one-click login
- ✅ Admin and user role differentiation
- ✅ Secure password hashing with salt
- ✅ CORS-enabled API for frontend integration

## 📱 Access Points

- **Frontend**: http://localhost:8000
- **Backend API**: http://localhost:8001  
- **API Documentation**: http://localhost:8001/docs

## 🏗️ Architecture

### Backend (FastAPI)
- **Port**: 8001
- **Database**: SQLite with user management
- **Features**: User registration, authentication, JWT tokens, admin dashboard
- **Location**: `auth_backend/`

### Frontend (Django + JavaScript)
- **Port**: 8000
- **Authentication**: JavaScript calls to FastAPI backend
- **Features**: Templates, forms, static files
- **Location**: `main/templates/`, `main/static/`

## 🧪 Testing

Run the integrated test suite:
```bash
python test_auth_system.py
```

This tests both the FastAPI backend and Django frontend integration.

## Project Structure

- `auth_backend/` - FastAPI authentication backend
  - `main.py` - FastAPI application with auth endpoints
  - `database.py` - SQLite database management
  - `models.py` - Pydantic models for API
  - `auth.py` - JWT token handling
- `main/` - Django frontend application
  - `templates/auth/` - Authentication templates  
  - `static/js/auth.js` - JavaScript authentication client
  - `views.py` - Django views (simplified for API integration)
- `real_estate_site/` - Django project configuration
- `main/static/` - Static files (CSS, JS, images)
- `main/templates/` - HTML templates