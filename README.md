# Brain Swarm - Autonomous Robotics Education Platform

Brain Swarm is a comprehensive educational robotics platform designed for universities, research institutions, and STEM programs. The platform combines swarm robotics simulation, user authentication, and educational content management to provide a complete learning environment for artificial intelligence, edge computing, and collective machine learning through swarm robotics.

## 🚀 Features

- **Educational Content Management**: Django-based frontend with blog system, user registration, and content management
- **Authentication & Booking System**: FastAPI backend with JWT authentication and robotics kit booking
- **Gazebo Simulation Environment**: Docker-based ROS/Gazebo simulation accessible via web browser
- **Admin Dashboard**: Complete user and content management system
- **Modular Architecture**: Independent components that can run separately

## 🏗️ System Architecture

### 1. Django Frontend (`/main/`)
- **Port**: 8000
- **Purpose**: Educational content, user interface, blog management
- **Features**: 
  - Home page with educational content about swarm robotics
  - User registration and authentication
  - Blog system for research updates
  - Contact and meeting scheduling forms
  - Admin dashboard for content management

### 2. FastAPI Authentication Backend (`/auth_backend/`)
- **Port**: 8001
- **Purpose**: User authentication, JWT tokens, booking system
- **Database**: SQLite (auth_backend.db)
- **API Documentation**: http://localhost:8001/docs
- **Features**:
  - User registration and authentication
  - JWT token-based security
  - Booking system for robotics services
  - Admin dashboard with statistics

### 3. Gazebo/ROS Simulation (`/docker-compose.yml`)
- **Ports**: 8080 (NoVNC web), 5901 (VNC)
- **Purpose**: Robotics simulation environment
- **Features**:
  - Full Gazebo simulation with GUI
  - Web browser access via NoVNC
  - Custom robot models and worlds
  - Independent Docker container

## 🚀 Quick Start

### One-Command Setup
```bash
./setup.sh
```

This script will install dependencies, test all components, and offer options to start individual or all services.

### Manual Setup

1. **Install Dependencies**
```bash
pip install -r requirements.txt
```

2. **Database Setup**
```bash
python manage.py migrate
```

3. **Start Django Frontend**
```bash
python manage.py runserver 8000
# Access at: http://localhost:8000
```

4. **Start FastAPI Auth Backend**
```bash
cd auth_backend && python3 main.py
# Access at: http://localhost:8001
# API Docs: http://localhost:8001/docs
```

5. **Start Gazebo Simulation (Optional)**
```bash
docker compose up gazebo
# Web Access: http://localhost:8080
# VNC Password: gazebo
```

## 📁 Project Structure

```
Brain-Swarm/
├── main/                   # Django Frontend Application
│   ├── templates/          # HTML templates
│   ├── static/            # CSS, JS, images
│   ├── views.py           # Django views
│   ├── models.py          # Database models
│   └── urls.py            # URL routing
├── auth_backend/          # FastAPI Authentication Backend
│   ├── main.py           # FastAPI application
│   ├── models.py         # Pydantic models
│   ├── database.py       # Database operations
│   └── auth.py           # JWT authentication
├── simulation/            # Simulation Backend
│   ├── main.py           # FastAPI simulation API
│   └── simulation.py     # Robot simulation logic
├── gazebo_data/          # Gazebo models and worlds
├── real_estate_site/     # Django project configuration
├── docker-compose.yml    # Gazebo/ROS Docker setup
├── setup.sh              # Main setup script
└── requirements.txt      # Python dependencies
```

## 🔐 Default Credentials

### Admin User (FastAPI Backend)
- **Username**: `admin`
- **Password**: `admin123`
- **Access**: Admin dashboard, all API endpoints

### Django Admin
Create with: `python3 manage.py createsuperuser`

### Gazebo VNC Access
- **Password**: `gazebo`

## 🧪 Testing

### Run All Tests
```bash
# Test authentication system
python3 test_auth_system.py

# Test simulation system
python test_simulation.py

# Test Gazebo setup
./test_gazebo_setup.sh
```

### Manual Testing

1. **Frontend**: Visit http://localhost:8000
2. **API Backend**: Visit http://localhost:8001/docs
3. **Gazebo Simulation**: Visit http://localhost:8080

## 🌟 Key Applications

- **STEM Curriculum Integration**: Robotics coursework and competitions
- **AI Research**: Machine learning experiments with swarm behavior
- **Field Robotics Testing**: Real-time environment testing
- **Edge Computing**: Distributed AI decision making
- **Educational Research**: Academic partnerships and learning outcomes

## 🔧 Configuration

### Environment Variables

**FastAPI Backend:**
- `ENVIRONMENT`: Set to 'production' for production deployment
- `LOG_LEVEL`: Logging level (default: INFO)

**Gazebo Simulation:**
- `VNC_PASSWORD`: VNC access password (default: gazebo)
- `VNC_RESOLUTION`: Screen resolution (default: 1024x768)
- `GAZEBO_MODEL_PATH`: Custom model paths

## 🛠️ Development

### Adding New Features
1. **Frontend**: Add views in `main/views.py` and templates
2. **Auth Backend**: Add endpoints in `auth_backend/main.py`
3. **Database**: Modify models in respective applications

### API Documentation
- **FastAPI Swagger**: http://localhost:8001/docs
- **FastAPI ReDoc**: http://localhost:8001/redoc

## 🤝 Contributing

1. Test your changes with `./setup.sh`
2. Run the complete test suite
3. Ensure all components work independently
4. Update documentation as needed

## 📞 Support

For issues or questions:
1. Check the setup script output for diagnostics
2. Verify all services are running on correct ports
3. Check logs from individual components
4. Ensure Docker is available for Gazebo features

## 📄 License

This project is designed for educational and research purposes. Built by researchers, for researchers.

---

**Brain Swarm's mission is to foster innovation in artificial intelligence, edge computing, and collective machine learning through accessible swarm robotics education.**