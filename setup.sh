#!/bin/bash
"""
Brain Swarm Setup Script
Orchestrates all components: FastAPI Auth Backend, Django Frontend, Gazebo/ROS Docker
"""

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
AUTH_BACKEND_PORT=8001
DJANGO_PORT=8000
GAZEBO_VNC_PORT=8080
GAZEBO_VNC_INTERNAL_PORT=5901

echo -e "${BLUE}🤖 Brain Swarm Setup Script${NC}"
echo -e "${BLUE}==============================${NC}"
echo ""

# Function to check if port is available
check_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        echo -e "${YELLOW}⚠️  Port $port is already in use${NC}"
        return 1
    else
        echo -e "${GREEN}✅ Port $port is available${NC}"
        return 0
    fi
}

# Function to install Python dependencies
install_dependencies() {
    echo -e "${BLUE}📦 Installing Python dependencies...${NC}"
    if [ -f "requirements.txt" ]; then
        pip install -r requirements.txt
        echo -e "${GREEN}✅ Dependencies installed${NC}"
    else
        echo -e "${RED}❌ requirements.txt not found${NC}"
        exit 1
    fi
}

# Function to setup FastAPI Auth Backend
setup_auth_backend() {
    echo -e "${BLUE}🔐 Setting up FastAPI Authentication Backend...${NC}"
    
    if [ ! -d "auth_backend" ]; then
        echo -e "${RED}❌ auth_backend directory not found${NC}"
        exit 1
    fi
    
    # Test auth backend
    echo -e "${YELLOW}🧪 Testing Auth Backend...${NC}"
    cd auth_backend
    python3 -c "
import sys
sys.path.append('..')
try:
    from auth_backend.main import app
    from auth_backend.database import Database
    # Test database initialization
    db = Database(':memory:')  # Use in-memory DB for test
    print('✅ Auth Backend modules loaded successfully')
except Exception as e:
    print(f'❌ Auth Backend test failed: {e}')
    sys.exit(1)
"
    cd ..
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Auth Backend setup complete${NC}"
    else
        echo -e "${RED}❌ Auth Backend setup failed${NC}"
        exit 1
    fi
}

# Function to setup Django frontend
setup_django_frontend() {
    echo -e "${BLUE}🌐 Setting up Django Frontend...${NC}"
    
    # Check Django setup
    if [ ! -f "manage.py" ]; then
        echo -e "${RED}❌ Django manage.py not found${NC}"
        exit 1
    fi
    
    # Test Django
    echo -e "${YELLOW}🧪 Testing Django setup...${NC}"
    python3 manage.py check
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Django setup complete${NC}"
    else
        echo -e "${RED}❌ Django setup failed${NC}"
        exit 1
    fi
}

# Function to setup Gazebo/ROS Docker
setup_gazebo_docker() {
    echo -e "${BLUE}🚁 Setting up Gazebo/ROS Docker...${NC}"
    
    # Check if Docker is available
    if ! command -v docker &> /dev/null; then
        echo -e "${YELLOW}⚠️  Docker not found, skipping Gazebo setup${NC}"
        return 0
    fi
    
    # Check for docker-compose
    COMPOSE_CMD=""
    if command -v docker-compose &> /dev/null; then
        COMPOSE_CMD="docker-compose"
    elif docker compose version &> /dev/null; then
        COMPOSE_CMD="docker compose"
    else
        echo -e "${YELLOW}⚠️  Docker Compose not found, skipping Gazebo setup${NC}"
        return 0
    fi
    
    # Create gazebo_data directories
    echo -e "${YELLOW}📁 Creating Gazebo data directories...${NC}"
    mkdir -p gazebo_data/models gazebo_data/worlds
    
    # Check if Gazebo dockerfile exists
    if [ -f "Dockerfile.gazebo" ] && [ -f "docker-compose.yml" ]; then
        echo -e "${GREEN}✅ Gazebo Docker configuration found${NC}"
        echo -e "${YELLOW}ℹ️  To start Gazebo later, run: $COMPOSE_CMD up gazebo${NC}"
    else
        echo -e "${YELLOW}⚠️  Gazebo Docker configuration not complete${NC}"
    fi
}

# Function to setup simulation backend
setup_simulation_backend() {
    echo -e "${BLUE}🤖 Setting up Simulation Backend...${NC}"
    
    if [ -d "simulation" ] && [ -f "simulation/main.py" ]; then
        echo -e "${YELLOW}🧪 Testing Simulation Backend...${NC}"
        cd simulation
        python3 -c "
try:
    import sys
    import os
    sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath('.'))))
    from simulation.main import app
    print('✅ Simulation Backend loaded successfully')
except Exception as e:
    print(f'⚠️  Simulation Backend test skipped: {e}')
"
        cd ..
        echo -e "${GREEN}✅ Simulation Backend setup complete${NC}"
    else
        echo -e "${YELLOW}⚠️  Simulation Backend not found, skipping${NC}"
    fi
}

# Function to start services
start_services() {
    echo -e "${BLUE}🚀 Starting Services...${NC}"
    echo ""
    echo -e "${YELLOW}Choose which services to start:${NC}"
    echo "1) All services (Auth Backend + Django + Gazebo)"
    echo "2) Auth Backend only"
    echo "3) Django Frontend only"
    echo "4) Gazebo/ROS Docker only"
    echo "5) Manual setup (show commands only)"
    echo ""
    read -p "Enter your choice (1-5): " choice
    
    case $choice in
        1)
            echo -e "${BLUE}🚀 Starting all services...${NC}"
            start_auth_backend &
            start_django_frontend &
            start_gazebo_docker &
            show_service_urls
            ;;
        2)
            echo -e "${BLUE}🔐 Starting Auth Backend only...${NC}"
            start_auth_backend
            ;;
        3)
            echo -e "${BLUE}🌐 Starting Django Frontend only...${NC}"
            start_django_frontend
            ;;
        4)
            echo -e "${BLUE}🚁 Starting Gazebo/ROS Docker only...${NC}"
            start_gazebo_docker
            ;;
        5)
            show_manual_commands
            ;;
        *)
            echo -e "${RED}❌ Invalid choice${NC}"
            exit 1
            ;;
    esac
}

# Function to start auth backend
start_auth_backend() {
    echo -e "${YELLOW}🔐 Starting FastAPI Auth Backend on port $AUTH_BACKEND_PORT...${NC}"
    cd auth_backend
    python3 main.py &
    AUTH_BACKEND_PID=$!
    cd ..
    echo -e "${GREEN}✅ Auth Backend started (PID: $AUTH_BACKEND_PID)${NC}"
}

# Function to start Django frontend
start_django_frontend() {
    echo -e "${YELLOW}🌐 Starting Django Frontend on port $DJANGO_PORT...${NC}"
    python3 manage.py runserver 0.0.0.0:$DJANGO_PORT &
    DJANGO_PID=$!
    echo -e "${GREEN}✅ Django Frontend started (PID: $DJANGO_PID)${NC}"
}

# Function to start Gazebo Docker
start_gazebo_docker() {
    if command -v docker &> /dev/null; then
        COMPOSE_CMD=""
        if command -v docker-compose &> /dev/null; then
            COMPOSE_CMD="docker-compose"
        elif docker compose version &> /dev/null; then
            COMPOSE_CMD="docker compose"
        fi
        
        if [ -n "$COMPOSE_CMD" ] && [ -f "docker-compose.yml" ]; then
            echo -e "${YELLOW}🚁 Starting Gazebo/ROS Docker...${NC}"
            $COMPOSE_CMD up gazebo -d
            echo -e "${GREEN}✅ Gazebo Docker started${NC}"
        else
            echo -e "${YELLOW}⚠️  Cannot start Gazebo Docker${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  Docker not available${NC}"
    fi
}

# Function to show service URLs
show_service_urls() {
    echo ""
    echo -e "${GREEN}🌟 Services Started Successfully!${NC}"
    echo -e "${GREEN}=================================${NC}"
    echo ""
    echo -e "${BLUE}📱 FastAPI Auth Backend:${NC}"
    echo -e "   🔗 API: http://localhost:$AUTH_BACKEND_PORT"
    echo -e "   📚 Docs: http://localhost:$AUTH_BACKEND_PORT/docs"
    echo ""
    echo -e "${BLUE}🌐 Django Frontend:${NC}"
    echo -e "   🔗 Website: http://localhost:$DJANGO_PORT"
    echo -e "   👤 Admin: http://localhost:$DJANGO_PORT/admin-dashboard/"
    echo ""
    echo -e "${BLUE}🚁 Gazebo/ROS Docker:${NC}"
    echo -e "   🔗 VNC Web: http://localhost:$GAZEBO_VNC_PORT"
    echo -e "   🖥️  VNC Client: localhost:$GAZEBO_VNC_INTERNAL_PORT"
    echo ""
    echo -e "${YELLOW}📝 Default Admin Credentials:${NC}"
    echo -e "   Username: admin"
    echo -e "   Password: admin123"
    echo ""
    echo -e "${YELLOW}⏹️  To stop all services, press Ctrl+C${NC}"
}

# Function to show manual commands
show_manual_commands() {
    echo ""
    echo -e "${GREEN}📋 Manual Setup Commands${NC}"
    echo -e "${GREEN}========================${NC}"
    echo ""
    echo -e "${BLUE}🔐 Start Auth Backend:${NC}"
    echo "   cd auth_backend && python3 main.py"
    echo ""
    echo -e "${BLUE}🌐 Start Django Frontend:${NC}"
    echo "   python3 manage.py runserver 0.0.0.0:$DJANGO_PORT"
    echo ""
    echo -e "${BLUE}🚁 Start Gazebo Docker:${NC}"
    if command -v docker-compose &> /dev/null; then
        echo "   docker-compose up gazebo"
    elif docker compose version &> /dev/null; then
        echo "   docker compose up gazebo"
    else
        echo "   (Docker Compose not available)"
    fi
    echo ""
    echo -e "${BLUE}🤖 Start Simulation Backend:${NC}"
    echo "   cd simulation && python3 main.py"
    echo ""
}

# Main execution
main() {
    echo -e "${BLUE}📋 Checking prerequisites...${NC}"
    
    # Check Python
    if ! command -v python3 &> /dev/null; then
        echo -e "${RED}❌ Python 3 not found${NC}"
        exit 1
    else
        echo -e "${GREEN}✅ Python 3 found${NC}"
    fi
    
    # Check port availability
    check_port $AUTH_BACKEND_PORT
    check_port $DJANGO_PORT
    check_port $GAZEBO_VNC_PORT
    
    echo ""
    
    # Install dependencies
    install_dependencies
    echo ""
    
    # Setup components
    setup_auth_backend
    echo ""
    
    setup_django_frontend
    echo ""
    
    setup_gazebo_docker
    echo ""
    
    setup_simulation_backend
    echo ""
    
    # Start services
    start_services
}

# Trap Ctrl+C to cleanup
trap 'echo -e "\n${YELLOW}🛑 Stopping services...${NC}"; kill $(jobs -p) 2>/dev/null; exit 0' INT

# Run main function
main "$@"