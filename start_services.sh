#!/bin/bash

# Brain Swarm Authentication System Setup Script
# This script starts both the FastAPI backend and Django frontend

echo "🚀 Starting Brain Swarm Authentication System..."
echo "======================================================"

# Function to kill background processes on exit
cleanup() {
    echo "🛑 Stopping services..."
    kill $FASTAPI_PID $DJANGO_PID 2>/dev/null
    exit 0
}

# Set up signal handling
trap cleanup SIGINT SIGTERM

# Start FastAPI backend
echo "🔐 Starting FastAPI Authentication Backend on port 8001..."
cd auth_backend
python main.py &
FASTAPI_PID=$!

# Wait a bit for FastAPI to start
sleep 3

# Start Django frontend
echo "🌐 Starting Django Frontend on port 8000..."
cd ..
python manage.py runserver 8000 &
DJANGO_PID=$!

# Wait a bit for Django to start
sleep 3

echo "✅ Both services are now running!"
echo ""
echo "📱 Access Points:"
echo "   🔗 Frontend: http://localhost:8000"
echo "   🔗 Backend API: http://localhost:8001"
echo "   📚 API Docs: http://localhost:8001/docs"
echo ""
echo "🧪 Demo Accounts:"
echo "   👤 Demo User: demo@brainswarm.com / demo123"
echo "   👨‍💼 Demo Admin: demoadmin@brainswarm.com / demoadmin123"
echo ""
echo "🔧 Original Admin: admin@brainswarm.com / admin123"
echo ""
echo "Press Ctrl+C to stop all services"

# Wait for both processes
wait $FASTAPI_PID $DJANGO_PID