#!/bin/bash

echo "🤖 Testing Gazebo VNC Setup..."

# Check if Docker is available
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed or not available"
    exit 1
fi

# Check for docker-compose (v1) or docker compose (v2)
COMPOSE_CMD=""
if command -v docker-compose &> /dev/null; then
    COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose is not installed or not available"
    echo "ℹ️  Install docker-compose or use Docker with compose plugin"
    exit 1
fi

echo "✅ Docker and Docker Compose are available ($COMPOSE_CMD)"

# Create gazebo_data directories if they don't exist
echo "📁 Creating gazebo_data directories..."
mkdir -p gazebo_data/models gazebo_data/worlds

echo "🐳 Building Gazebo VNC Docker image..."

# Try to build the main Dockerfile first
if docker build -f Dockerfile.gazebo -t gazebo-vnc:latest . 2>/dev/null; then
    echo "✅ Main Docker image built successfully"
    BUILD_SUCCESS=true
elif docker build -f Dockerfile.simple -t gazebo-vnc:latest . 2>/dev/null; then
    echo "✅ Simplified Docker image built successfully"
    echo "⚠️  Note: This build may have limited NoVNC support"
    BUILD_SUCCESS=true
else
    echo "❌ Docker build failed with both Dockerfiles"
    echo "ℹ️  This may be due to network connectivity issues"
    echo "ℹ️  Try building on a system with internet access"
    BUILD_SUCCESS=false
fi

if [ "$BUILD_SUCCESS" = true ]; then
    echo ""
    echo "🚀 Ready to start Gazebo simulation!"
    echo ""
    echo "Next steps:"
    echo "  1. Run: $COMPOSE_CMD up -d"
    echo "  2. Wait a few moments for services to start"
    echo "  3. Check logs: $COMPOSE_CMD logs -f gazebo"
    echo "  4. Open browser to: http://localhost:8080 (if NoVNC is available)"
    echo "  5. Or connect VNC client to: localhost:5901"
    echo ""
    echo "Default VNC password: gazebo"
    echo ""
    echo "Troubleshooting:"
    echo "  - If NoVNC doesn't work, use a VNC client directly"
    echo "  - Common VNC clients: TightVNC, RealVNC, TigerVNC"
    echo "  - Make sure ports 5901 and 8080 are not in use"
else
    echo ""
    echo "🛠️  Build failed, but you can still try:"
    echo "  1. Check your internet connection"
    echo "  2. Try: docker pull osrf/ros:noetic-desktop-full"
    echo "  3. Run the test again"
    echo ""
    echo "Manual verification steps:"
    echo "  1. Check if base image is available: docker images | grep ros"
    echo "  2. Test basic connectivity: ping -c 3 archive.ubuntu.com"
fi