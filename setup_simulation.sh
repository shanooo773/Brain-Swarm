#!/bin/bash
"""
Setup script for TurtleBot simulation environment.
Builds Docker image and prepares simulation directories.
"""

set -e

echo "🤖 Setting up TurtleBot simulation environment..."

# Create simulation directories
echo "📁 Creating simulation directories..."
mkdir -p /tmp/simulation_scripts
mkdir -p /tmp/simulation_results

# Copy simulation script to mount directory
echo "📋 Copying simulation scripts..."
cp simulation/simulation.py /tmp/simulation_scripts/

# Build Docker image
echo "🐳 Building Docker image..."
docker build -f Dockerfile.simulation -t turtlebot-simulation:latest .

echo "✅ Setup complete!"
echo ""
echo "🚀 You can now start the FastAPI simulation backend with:"
echo "   cd simulation && python main.py"
echo ""
echo "📡 Or run a direct simulation with:"
echo "   docker run --rm -v /tmp/simulation_scripts:/app:ro turtlebot-simulation:latest"