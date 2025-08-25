#!/bin/bash

echo "🤖 Testing Gazebo VNC Setup..."

# Check if Docker is available
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed or not available"
    exit 1
fi

# Check if docker-compose is available  
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed or not available"
    exit 1
fi

echo "✅ Docker and docker-compose are available"

# Create gazebo_data directories if they don't exist
echo "📁 Creating gazebo_data directories..."
mkdir -p gazebo_data/models gazebo_data/worlds

echo "🐳 Building Gazebo VNC Docker image..."
if docker build -f Dockerfile.gazebo -t gazebo-vnc:latest .; then
    echo "✅ Docker image built successfully"
else
    echo "❌ Docker build failed"
    exit 1
fi

echo ""
echo "🚀 Ready to start Gazebo simulation!"
echo ""
echo "Next steps:"
echo "  1. Run: docker-compose up -d"
echo "  2. Wait a few moments for services to start"
echo "  3. Open browser to: http://localhost:8080"
echo "  4. Click 'Connect' and start using Gazebo!"
echo ""
echo "Default VNC password: gazebo"