# Robot Simulation Backend Documentation

## Overview

This FastAPI-based robot simulation backend provides Docker SDK integration for TurtleBot simulation. It addresses common issues with function attribute errors and uses modern FastAPI patterns.

## Fixed Issues

### 1. 'function' object has no attribute 'run' Error

**Problem**: Code was trying to call `run.run()` where `run` is a function, not an object.

**Solution**: 
- Use class-based approach: `TurtleBotSimulation().run()`
- Or call function directly: `run()` instead of `run.run()`

#### Wrong Pattern (Causes Error)
```python
# ❌ This causes 'function' object has no attribute 'run'
run.run()  # where 'run' is a function
```

#### Correct Patterns
```python
# ✅ Option 1: Call function directly
run()

# ✅ Option 2: Use class-based approach  
sim = TurtleBotSimulation()
sim.run()
```

### 2. Deprecated FastAPI on_event

**Problem**: FastAPI warned about deprecated `on_event` usage.

**Solution**: Use modern `lifespan` context manager.

#### Old Pattern (Deprecated)
```python
@app.on_event("startup")
async def startup():
    # initialization code
    pass

@app.on_event("shutdown") 
async def shutdown():
    # cleanup code
    pass
```

#### New Pattern (Modern)
```python
@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    # initialization code
    yield
    # Shutdown 
    # cleanup code

app = FastAPI(lifespan=lifespan)
```

## Architecture

### Components

1. **FastAPI Backend** (`simulation/main.py`)
   - Main API server with endpoints for simulation control
   - Uses modern lifespan handlers instead of deprecated on_event
   - Proper error handling and Docker fallback

2. **TurtleBot Simulation Class** (`simulation/main.py`)
   - Encapsulates Docker SDK operations
   - Handles both Docker SDK and CLI fallback execution
   - Proper class structure to avoid function attribute errors

3. **Simulation Script** (`simulation/simulation.py`)
   - Runs inside Docker container
   - Demonstrates correct function/class calling patterns
   - Configurable via environment variables

4. **Docker Integration**
   - Custom Docker image for TurtleBot simulation
   - Volume mounting for script sharing
   - Proper container lifecycle management

## API Endpoints

### GET `/`
Health check endpoint showing backend status.

**Response:**
```json
{
  "message": "Brain Swarm Robot Simulation Backend",
  "status": "operational", 
  "docker_available": true
}
```

### POST `/simulate`
Execute robot simulation.

**Request:**
```json
{
  "script_path": "/app/simulation.py",
  "container_name": "turtlebot-sim",
  "image_name": "turtlebot-simulation:latest",
  "parameters": {
    "max_time": "30",
    "angular_velocity": "0.5",
    "linear_velocity": "1.0"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Simulation completed successfully",
  "output": "Simulation logs...",
  "error": null
}
```

### GET `/status`
Get detailed backend status.

**Response:**
```json
{
  "status": "ready",
  "docker_available": true,
  "docker_client_status": "connected"
}
```

## Setup Instructions

### 1. Install Dependencies
```bash
pip install -r requirements.txt
```

### 2. Build Docker Image
```bash
./setup_simulation.sh
```

### 3. Start FastAPI Server
```bash
cd simulation
python main.py
```

The server will start on `http://localhost:8000`

### 4. Test Simulation
```bash
# Run tests
python test_simulation.py

# Manual test via API
curl -X POST "http://localhost:8000/simulate" \
  -H "Content-Type: application/json" \
  -d '{
    "parameters": {
      "max_time": "10",
      "angular_velocity": "0.3"
    }
  }'
```

## Docker Integration

### Building Simulation Image
```bash
docker build -f Dockerfile.simulation -t turtlebot-simulation:latest .
```

### Running Simulation Directly
```bash
docker run --rm \
  -v /tmp/simulation_scripts:/app:ro \
  -e MAX_TIME=30 \
  -e ANGULAR_VELOCITY=0.5 \
  turtlebot-simulation:latest
```

## Error Handling

### Docker SDK Unavailable
When Docker SDK fails, the backend automatically falls back to CLI execution:

```python
if self.is_docker_available:
    return self._run_with_docker_sdk(request)
else:
    return self._run_with_cli_fallback(request)
```

### Container Execution Failures
All container execution errors are caught and returned as structured responses:

```python
try:
    # Docker execution
    result = container.wait()
except Exception as e:
    return SimulationResponse(
        success=False,
        message="Docker execution failed",
        error=str(e)
    )
```

## Testing

Run the test suite to validate all functionality:

```bash
python test_simulation.py
```

Tests cover:
- Proper function/class calling patterns
- Docker SDK integration
- Error handling scenarios  
- Model validation
- Fallback mechanisms

## Configuration

### Environment Variables

**For FastAPI Backend:**
- `ENVIRONMENT`: Set to 'production' for production deployment
- `LOG_LEVEL`: Logging level (default: INFO)

**For Simulation Container:**
- `MAX_TIME`: Maximum simulation time in seconds
- `ANGULAR_VELOCITY`: Robot angular velocity 
- `LINEAR_VELOCITY`: Robot linear velocity
- `SIMULATION_MODE`: Simulation mode ('container' for Docker)

### Volume Mounts

The simulation uses volume mounts to share scripts:
- Host: `/tmp/simulation_scripts`
- Container: `/app` (read-only)

## Production Deployment

### Docker Compose Example
```yaml
version: '3.8'
services:
  simulation-backend:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
      - ./simulation_scripts:/tmp/simulation_scripts
    environment:
      - ENVIRONMENT=production
```

### Security Considerations

1. **Docker Socket Access**: Required for Docker SDK functionality
2. **Volume Mounts**: Use read-only mounts for simulation scripts
3. **Container Isolation**: Simulation containers run with limited privileges
4. **Input Validation**: All API inputs are validated using Pydantic models

## Troubleshooting

### Common Issues

1. **"Docker execution failed: 'function' object has no attribute 'run'"**
   - Fixed by using proper class instantiation pattern
   - Ensure you're calling `instance.run()` not `function.run()`

2. **"on_event is deprecated" Warning**
   - Fixed by using modern lifespan handlers
   - No action needed with current implementation

3. **Container Image Not Found**
   - Run `./setup_simulation.sh` to build the Docker image
   - Verify image exists: `docker images | grep turtlebot-simulation`

4. **Permission Denied on Volume Mounts**
   - Ensure `/tmp/simulation_scripts` directory exists and is writable
   - Check Docker daemon permissions

### Debug Mode

Enable debug logging:
```python
import logging
logging.getLogger().setLevel(logging.DEBUG)
```

## Contributing

When adding new simulation features:

1. Follow the established class-based pattern
2. Add comprehensive tests
3. Use proper error handling
4. Update documentation
5. Validate with both Docker SDK and CLI fallback modes