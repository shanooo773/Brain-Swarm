#!/bin/bash
"""
Demo script showing Brain Swarm system in action
"""

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🎬 Brain Swarm System Demo${NC}"
echo -e "${BLUE}=========================${NC}"
echo ""

echo -e "${YELLOW}📋 This demo will:${NC}"
echo "1. Start FastAPI Auth Backend (port 8001)"
echo "2. Start Django Frontend (port 8000)"
echo "3. Run automated tests"
echo "4. Show service URLs"
echo ""

read -p "Press Enter to continue..."
echo ""

# Start auth backend
echo -e "${BLUE}🔐 Starting Auth Backend...${NC}"
cd auth_backend
python3 main.py &
AUTH_PID=$!
cd ..
sleep 5

# Start Django frontend
echo -e "${BLUE}🌐 Starting Django Frontend...${NC}"
python3 manage.py runserver 0.0.0.0:8000 &
DJANGO_PID=$!
sleep 5

# Run tests
echo -e "${BLUE}🧪 Running Tests...${NC}"
python3 test_auth_system.py

# Show URLs
echo ""
echo -e "${GREEN}🌟 Demo Complete! Services Running:${NC}"
echo -e "${GREEN}===================================${NC}"
echo ""
echo -e "${BLUE}📱 FastAPI Auth Backend:${NC}"
echo -e "   🔗 API: http://localhost:8001"
echo -e "   📚 Docs: http://localhost:8001/docs"
echo ""
echo -e "${BLUE}🌐 Django Frontend:${NC}"
echo -e "   🔗 Website: http://localhost:8000"
echo -e "   👤 Sign-in: http://localhost:8000/sign-in/"
echo -e "   📝 Sign-up: http://localhost:8000/sign-up/"
echo ""
echo -e "${YELLOW}📝 Admin Credentials:${NC}"
echo -e "   Username: admin"
echo -e "   Password: admin123"
echo ""
echo -e "${YELLOW}⏹️  Press Ctrl+C to stop all services${NC}"

# Cleanup function
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Stopping services...${NC}"
    kill $AUTH_PID $DJANGO_PID 2>/dev/null
    echo -e "${GREEN}✅ Demo ended${NC}"
    exit 0
}

# Trap Ctrl+C
trap cleanup INT

# Keep running
wait