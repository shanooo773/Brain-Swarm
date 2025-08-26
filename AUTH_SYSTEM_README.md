# Brain Swarm Authentication System

This repository now includes a complete authentication and booking system with separate FastAPI backend and Django frontend, plus Gazebo/ROS Docker integration.

## 🚀 Quick Start

### One-Command Setup
```bash
./setup.sh
```

This script will:
- Install dependencies
- Test all components  
- Offer options to start individual or all services

### Manual Setup

1. **Install Dependencies**
```bash
pip install -r requirements.txt
```

2. **Start FastAPI Auth Backend**
```bash
cd auth_backend && python3 main.py
# Runs on http://localhost:8001
```

3. **Start Django Frontend**
```bash
python3 manage.py runserver 8000
# Runs on http://localhost:8000
```

4. **Start Gazebo/ROS Docker (Optional)**
```bash
docker compose up gazebo
# VNC Web: http://localhost:8080
# VNC Client: localhost:5901
```

## 🏗️ System Architecture

### 1. FastAPI Authentication Backend (`/auth_backend/`)
- **Port**: 8001
- **Purpose**: Handles user authentication, JWT tokens, booking system
- **Database**: SQLite (auth_backend.db)
- **API Documentation**: http://localhost:8001/docs

#### Key Features:
- User registration and authentication
- JWT token-based security
- Booking system for robotics services
- Admin dashboard with statistics
- CORS enabled for frontend integration

#### API Endpoints:
- `POST /auth/signup` - User registration
- `POST /auth/signin` - User login
- `GET /auth/me` - Get current user info
- `POST /bookings/` - Create booking
- `GET /bookings/` - Get user bookings
- `GET /admin/stats` - Admin dashboard (admin only)

### 2. Django Frontend (`/main/`)
- **Port**: 8000
- **Purpose**: User interface, forms, blog system
- **Database**: SQLite (db.sqlite3)
- **Admin**: http://localhost:8000/admin-dashboard/

#### Key Features:
- User-friendly sign-in/sign-up forms
- Blog system with admin management
- Form submissions and contact system
- Responsive web design
- Admin dashboard for content management

### 3. Gazebo/ROS Docker (`/docker-compose.yml`)
- **Ports**: 8080 (NoVNC), 5901 (VNC)
- **Purpose**: Robotics simulation environment
- **Features**: VNC access to Gazebo simulation

### 4. Simulation Backend (`/simulation/`)
- **Purpose**: Handles TurtleBot simulations
- **Integration**: Works with Docker containers

## 🔐 Default Credentials

### Admin User (FastAPI Backend)
- **Username**: `admin`
- **Password**: `admin123`
- **Access**: Admin dashboard, all API endpoints

### Django Admin (if needed)
Create with: `python3 manage.py createsuperuser`

## 🧪 Testing the System

### Automated Test
```bash
python3 test_auth_system.py
```

This will test:
- FastAPI auth backend functionality
- Django frontend accessibility
- User registration, login, and booking
- Admin dashboard access

### Manual Testing

1. **FastAPI Backend**:
   - Visit http://localhost:8001/docs
   - Test API endpoints using the interactive documentation
   - Try sign-up, sign-in, and booking creation

2. **Django Frontend**:
   - Visit http://localhost:8000
   - Navigate to sign-in/sign-up pages
   - Test form submissions

3. **Integration Test**:
   - Register user via FastAPI
   - Access Django frontend
   - Create bookings via API
   - Check admin dashboard

## 📁 Project Structure

```
Brain-Swarm/
├── auth_backend/           # FastAPI Authentication Backend
│   ├── __init__.py
│   ├── main.py            # FastAPI application
│   ├── models.py          # Pydantic models
│   ├── database.py        # SQLite database layer
│   └── auth.py            # JWT token management
├── main/                  # Django Frontend
│   ├── models.py          # Django models
│   ├── views.py           # Django views
│   ├── urls.py            # URL routing
│   └── templates/         # HTML templates
├── simulation/            # Simulation Backend
│   ├── main.py            # FastAPI simulation API
│   └── simulation.py      # Core simulation logic
├── gazebo_data/           # Gazebo models and worlds
├── setup.sh               # Main setup script
├── test_auth_system.py    # Comprehensive test script
├── docker-compose.yml     # Gazebo/ROS Docker config
└── requirements.txt       # Python dependencies
```

## 🔧 Configuration

### Environment Variables
- `AUTH_BACKEND_PORT`: FastAPI port (default: 8001)
- `DJANGO_PORT`: Django port (default: 8000)
- `VNC_PASSWORD`: Gazebo VNC password (default: gazebo)

### Database Configuration
- **FastAPI**: SQLite database (`auth_backend.db`)
- **Django**: SQLite database (`db.sqlite3`)
- Both databases are created automatically

## 🌟 Features Implemented

### ✅ Completed Requirements
- [x] Separate Python FastAPI backend for authentication
- [x] Sign-in/sign-up system with JWT tokens
- [x] Booking system for robotics services
- [x] Admin dashboard with statistics
- [x] Django frontend for user interface
- [x] Independent operation from Gazebo/ROS
- [x] Docker configuration for Gazebo/ROS
- [x] Comprehensive setup script
- [x] Testing and validation system

### 🚀 Key Benefits
1. **Separation of Concerns**: Auth backend, frontend, and simulation are independent
2. **Scalability**: Each component can be scaled separately
3. **Modern Architecture**: FastAPI for APIs, Django for UI
4. **Easy Setup**: One-command deployment
5. **Comprehensive Testing**: Automated validation
6. **Docker Integration**: Containerized Gazebo/ROS

## 🛠️ Development

### Adding New Features
1. **Auth Backend**: Add endpoints in `auth_backend/main.py`
2. **Frontend**: Add views in `main/views.py` and templates
3. **Database**: Modify `auth_backend/database.py` for new tables

### API Documentation
The FastAPI backend automatically generates API documentation:
- **Swagger UI**: http://localhost:8001/docs
- **ReDoc**: http://localhost:8001/redoc

## 🤝 Contributing

1. Test your changes with `./setup.sh`
2. Run the test suite: `python3 test_auth_system.py`
3. Ensure all components work independently
4. Update documentation as needed

## 📞 Support

For issues or questions:
1. Check the automated test output
2. Verify all services are running on correct ports
3. Check logs from individual components
4. Ensure Docker is available for Gazebo features