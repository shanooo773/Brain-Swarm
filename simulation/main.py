"""
FastAPI-based Robot Simulation Backend
Handles TurtleBot simulation using Docker SDK with proper error handling.
"""

import logging
import subprocess
from contextlib import asynccontextmanager
from typing import Dict, Any, Optional
import docker
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class SimulationRequest(BaseModel):
    """Request model for simulation execution."""
    script_path: str = "/app/simulation.py"
    container_name: str = "turtlebot-sim"
    image_name: str = "turtlebot-simulation:latest"
    parameters: Dict[str, Any] = {}


class SimulationResponse(BaseModel):
    """Response model for simulation results."""
    success: bool
    message: str
    output: Optional[str] = None
    error: Optional[str] = None


class TurtleBotSimulation:
    """
    TurtleBot simulation class with proper structure.
    Fixes the 'function' object has no attribute 'run' error by using class-based approach.
    """
    
    def __init__(self):
        self.docker_client = None
        self.is_docker_available = False
        self._initialize_docker()
    
    def _initialize_docker(self):
        """Initialize Docker client with error handling."""
        try:
            self.docker_client = docker.from_env()
            # Test Docker connection
            self.docker_client.ping()
            self.is_docker_available = True
            logger.info("Docker SDK initialized successfully")
        except Exception as e:
            logger.warning(f"Docker SDK initialization failed: {e}")
            self.is_docker_available = False
    
    def run(self, request: SimulationRequest) -> SimulationResponse:
        """
        Main simulation execution method.
        This is the correct pattern: sim.run() where sim is a class instance.
        """
        logger.info(f"Starting simulation with container: {request.container_name}")
        
        if self.is_docker_available:
            return self._run_with_docker_sdk(request)
        else:
            logger.info("Docker SDK not available, falling back to CLI")
            return self._run_with_cli_fallback(request)
    
    def _run_with_docker_sdk(self, request: SimulationRequest) -> SimulationResponse:
        """Execute simulation using Docker SDK."""
        try:
            # Check if image exists
            try:
                self.docker_client.images.get(request.image_name)
            except docker.errors.ImageNotFound:
                return SimulationResponse(
                    success=False,
                    message=f"Docker image {request.image_name} not found",
                    error="Image not available"
                )
            
            # Run container
            container = self.docker_client.containers.run(
                image=request.image_name,
                command=f"python3 {request.script_path}",
                detach=True,
                name=request.container_name,
                remove=True,
                volumes={
                    '/tmp/simulation_scripts': {'bind': '/app', 'mode': 'ro'}
                },
                environment=request.parameters
            )
            
            # Wait for container to complete
            result = container.wait()
            logs = container.logs().decode('utf-8')
            
            if result['StatusCode'] == 0:
                return SimulationResponse(
                    success=True,
                    message="Simulation completed successfully",
                    output=logs
                )
            else:
                return SimulationResponse(
                    success=False,
                    message="Simulation failed",
                    error=logs
                )
                
        except Exception as e:
            logger.error(f"Docker execution failed: {e}")
            return SimulationResponse(
                success=False,
                message="Docker execution failed",
                error=str(e)
            )
    
    def _run_with_cli_fallback(self, request: SimulationRequest) -> SimulationResponse:
        """Fallback to CLI execution when Docker SDK fails."""
        try:
            # Construct docker run command
            docker_cmd = [
                "docker", "run", "--rm",
                "--name", request.container_name,
                "-v", "/tmp/simulation_scripts:/app:ro"
            ]
            
            # Add environment variables
            for key, value in request.parameters.items():
                docker_cmd.extend(["-e", f"{key}={value}"])
            
            # Add image and command
            docker_cmd.extend([request.image_name, "python3", request.script_path])
            
            # Execute command
            result = subprocess.run(
                docker_cmd,
                capture_output=True,
                text=True,
                timeout=300  # 5 minute timeout
            )
            
            if result.returncode == 0:
                return SimulationResponse(
                    success=True,
                    message="Simulation completed successfully (CLI fallback)",
                    output=result.stdout
                )
            else:
                return SimulationResponse(
                    success=False,
                    message="Simulation failed (CLI fallback)",
                    error=result.stderr
                )
                
        except subprocess.TimeoutExpired:
            return SimulationResponse(
                success=False,
                message="Simulation timed out",
                error="Execution exceeded 5 minute timeout"
            )
        except Exception as e:
            logger.error(f"CLI fallback failed: {e}")
            return SimulationResponse(
                success=False,
                message="CLI fallback failed",
                error=str(e)
            )


# Global simulation instance
simulation_instance = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Modern FastAPI lifespan handler.
    Replaces deprecated on_event startup/shutdown handlers.
    """
    global simulation_instance
    
    # Startup
    logger.info("Initializing robot simulation backend...")
    simulation_instance = TurtleBotSimulation()
    logger.info("Robot simulation backend ready")
    
    yield
    
    # Shutdown
    logger.info("Shutting down robot simulation backend...")
    if simulation_instance and simulation_instance.docker_client:
        simulation_instance.docker_client.close()
    logger.info("Robot simulation backend shutdown complete")


# Create FastAPI app with modern lifespan handler
app = FastAPI(
    title="Brain Swarm Robot Simulation Backend",
    description="FastAPI backend for TurtleBot simulation using Docker",
    version="1.0.0",
    lifespan=lifespan
)


@app.get("/")
async def root():
    """Health check endpoint."""
    return {
        "message": "Brain Swarm Robot Simulation Backend",
        "status": "operational",
        "docker_available": simulation_instance.is_docker_available if simulation_instance else False
    }


@app.post("/simulate", response_model=SimulationResponse)
async def run_simulation(request: SimulationRequest):
    """
    Execute robot simulation.
    
    This endpoint demonstrates the correct pattern:
    - simulation_instance.run() calls the run method on a class instance
    - NOT run.run() which would cause 'function' object has no attribute 'run' error
    """
    if not simulation_instance:
        raise HTTPException(status_code=500, detail="Simulation backend not initialized")
    
    try:
        # This is the CORRECT pattern: instance.run()
        result = simulation_instance.run(request)
        return result
    except Exception as e:
        logger.error(f"Simulation execution error: {e}")
        raise HTTPException(status_code=500, detail=f"Simulation failed: {str(e)}")


@app.get("/status")
async def get_status():
    """Get simulation backend status."""
    if not simulation_instance:
        return {"status": "not_initialized"}
    
    return {
        "status": "ready",
        "docker_available": simulation_instance.is_docker_available,
        "docker_client_status": "connected" if simulation_instance.docker_client else "disconnected"
    }


def run_server():
    """
    Function to run the FastAPI server.
    Note: This is a function, not a class method, so it should be called as run_server()
    """
    uvicorn.run(
        "simulation.main:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )


if __name__ == "__main__":
    # Demonstration of correct calling patterns:
    
    # WRONG: This would cause 'function' object has no attribute 'run' error
    # run_server.run()  # ❌ run_server is a function, not an object
    
    # CORRECT: Call the function directly
    run_server()  # ✅ Calls the function
    
    # CORRECT: If using a class-based approach
    # sim = TurtleBotSimulation()  # Create instance
    # sim.run(request)  # ✅ Call method on instance