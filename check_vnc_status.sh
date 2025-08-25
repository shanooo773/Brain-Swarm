#!/bin/bash

echo "🔍 Gazebo VNC Connectivity Test"
echo "================================"

# Detect docker compose command
COMPOSE_CMD=""
if command -v docker-compose &> /dev/null; then
    COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose not found"
    exit 1
fi

# Function to check if a port is accessible
check_port() {
    local host=$1
    local port=$2
    local service=$3
    
    if command -v nc &> /dev/null; then
        if nc -z -w3 $host $port 2>/dev/null; then
            echo "✅ $service ($host:$port) - Accessible"
        else
            echo "❌ $service ($host:$port) - Not accessible"
        fi
    else
        echo "⚠️  netcat not available, skipping $service port check"
    fi
}

# Check if docker-compose is running
echo ""
echo "📋 Checking Docker Compose Status:"
if $COMPOSE_CMD ps | grep gazebo | grep Up > /dev/null; then
    echo "✅ Gazebo container is running"
    
    # Get container IP and ports
    echo ""
    echo "🔌 Port Mappings:"
    $COMPOSE_CMD port gazebo 8080 2>/dev/null && echo "✅ NoVNC port mapped" || echo "❌ NoVNC port not mapped"
    $COMPOSE_CMD port gazebo 5901 2>/dev/null && echo "✅ VNC port mapped" || echo "❌ VNC port not mapped"
    
    # Check port accessibility
    echo ""
    echo "🌐 Port Accessibility:"
    check_port localhost 8080 "NoVNC"
    check_port localhost 5901 "VNC"
    
    # Check container logs for service status
    echo ""
    echo "📋 Service Status in Container:"
    if $COMPOSE_CMD logs gazebo 2>/dev/null | grep -i "starting vnc" > /dev/null; then
        echo "✅ VNC server startup detected in logs"
    else
        echo "⚠️  VNC server startup not found in logs"
    fi
    
    if $COMPOSE_CMD logs gazebo 2>/dev/null | grep -i "starting novnc\|websockify" > /dev/null; then
        echo "✅ NoVNC/websockify startup detected in logs"
    else
        echo "⚠️  NoVNC/websockify startup not found in logs"
    fi
    
    # Display recent logs
    echo ""
    echo "📄 Recent Container Logs:"
    echo "------------------------"
    $COMPOSE_CMD logs --tail=10 gazebo
    
else
    echo "❌ Gazebo container is not running"
    echo ""
    echo "🚀 To start the container, run:"
    echo "   $COMPOSE_CMD up -d"
    echo ""
    echo "📋 Current containers:"
    $COMPOSE_CMD ps
fi

echo ""
echo "🔗 Access URLs:"
echo "   NoVNC (Browser): http://localhost:8080"
echo "   VNC (Client):    localhost:5901"
echo "   Password:        gazebo"
echo ""
echo "🛠️  Troubleshooting:"
echo "   - Check logs: $COMPOSE_CMD logs gazebo"
echo "   - Restart: $COMPOSE_CMD restart gazebo"
echo "   - Rebuild: $COMPOSE_CMD up -d --build"