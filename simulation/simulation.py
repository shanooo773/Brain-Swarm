#!/usr/bin/env python3
"""
TurtleBot Simulation Script
This script runs inside the Docker container for robot simulation.
"""

import os
import sys
import time
import logging
import json
from typing import Dict, Any

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)


class TurtleBotSimulator:
    """
    TurtleBot simulator class.
    Demonstrates proper class structure to avoid function attribute errors.
    """
    
    def __init__(self, parameters: Dict[str, Any] = None):
        self.parameters = parameters or {}
        self.position = {"x": 0.0, "y": 0.0, "theta": 0.0}
        self.simulation_time = 0.0
        self.max_simulation_time = float(self.parameters.get("max_time", 30.0))
        
    def initialize(self):
        """Initialize the simulation environment."""
        logger.info("Initializing TurtleBot simulation...")
        logger.info(f"Parameters: {self.parameters}")
        logger.info(f"Max simulation time: {self.max_simulation_time} seconds")
        
    def run(self):
        """
        Main simulation loop.
        This method is called on a class instance: simulator.run()
        """
        logger.info("Starting TurtleBot simulation...")
        
        try:
            self.initialize()
            
            # Simulation loop
            dt = 0.1  # 10 Hz simulation
            steps = int(self.max_simulation_time / dt)
            
            for step in range(steps):
                self.simulation_time = step * dt
                self.update_robot_state(dt)
                
                # Log progress every 5 seconds
                if step % 50 == 0:
                    logger.info(f"Simulation time: {self.simulation_time:.1f}s, Position: {self.position}")
                
                # Simulate real-time execution
                time.sleep(dt)
            
            logger.info("Simulation completed successfully")
            self.save_results()
            
        except KeyboardInterrupt:
            logger.info("Simulation interrupted by user")
        except Exception as e:
            logger.error(f"Simulation error: {e}")
            sys.exit(1)
    
    def update_robot_state(self, dt: float):
        """Update robot position and orientation."""
        # Simple circular motion for demonstration
        angular_velocity = float(self.parameters.get("angular_velocity", 0.5))
        linear_velocity = float(self.parameters.get("linear_velocity", 1.0))
        
        self.position["theta"] += angular_velocity * dt
        self.position["x"] += linear_velocity * dt * cos_approximation(self.position["theta"])
        self.position["y"] += linear_velocity * dt * sin_approximation(self.position["theta"])
    
    def save_results(self):
        """Save simulation results."""
        results = {
            "final_position": self.position,
            "simulation_time": self.simulation_time,
            "parameters": self.parameters,
            "status": "completed"
        }
        
        results_path = "/tmp/simulation_results.json"
        try:
            with open(results_path, 'w') as f:
                json.dump(results, f, indent=2)
            logger.info(f"Results saved to {results_path}")
        except Exception as e:
            logger.error(f"Failed to save results: {e}")


def cos_approximation(angle: float) -> float:
    """Simple cosine approximation for demonstration."""
    import math
    return math.cos(angle)


def sin_approximation(angle: float) -> float:
    """Simple sine approximation for demonstration."""
    import math
    return math.sin(angle)


def load_parameters() -> Dict[str, Any]:
    """Load simulation parameters from environment variables."""
    parameters = {}
    
    # Read common simulation parameters
    env_vars = [
        "max_time", "angular_velocity", "linear_velocity",
        "simulation_mode", "robot_model", "environment"
    ]
    
    for var in env_vars:
        value = os.environ.get(var.upper())
        if value:
            # Try to convert to appropriate type
            try:
                if '.' in value:
                    parameters[var] = float(value)
                else:
                    parameters[var] = int(value)
            except ValueError:
                parameters[var] = value
    
    return parameters


def run():
    """
    Main entry point function.
    
    IMPORTANT: This demonstrates the correct calling pattern.
    This function creates a class instance and calls its run method.
    """
    logger.info("TurtleBot simulation script starting...")
    
    # Load parameters from environment
    parameters = load_parameters()
    
    # Create simulator instance and run
    # CORRECT pattern: Create instance, then call method
    simulator = TurtleBotSimulator(parameters)
    simulator.run()  # ✅ This is correct: instance.run()
    
    logger.info("TurtleBot simulation script completed")


if __name__ == "__main__":
    # Demonstration of WRONG vs CORRECT patterns:
    
    # WRONG: This would cause 'function' object has no attribute 'run'
    # run.run()  # ❌ run is a function, not an object with a run method
    
    # CORRECT: Call the function directly
    run()  # ✅ This calls the run function
    
    # Alternative CORRECT pattern if you want to access the simulator object:
    # simulator = TurtleBotSimulator()
    # simulator.run()  # ✅ This is also correct