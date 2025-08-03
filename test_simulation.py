#!/usr/bin/env python3
"""
Test script for TurtleBot simulation functionality.
Validates the fixes for 'function' object has no attribute 'run' error.
"""

import sys
import os
import unittest
from unittest.mock import Mock, patch, MagicMock

# Add simulation directory to path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'simulation'))

from simulation.main import TurtleBotSimulation, SimulationRequest, SimulationResponse
from simulation.simulation import TurtleBotSimulator, run


class TestSimulationFunctionCalling(unittest.TestCase):
    """Test proper function and method calling patterns."""
    
    def setUp(self):
        """Set up test fixtures."""
        self.simulation_request = SimulationRequest(
            script_path="/app/simulation.py",
            container_name="test-turtlebot",
            image_name="turtlebot-simulation:latest",
            parameters={"max_time": "10", "angular_velocity": "0.5"}
        )
    
    def test_turtlebot_simulation_class_instantiation(self):
        """Test that TurtleBotSimulation class can be instantiated properly."""
        # This tests the fix for the main issue: proper class instantiation
        simulation = TurtleBotSimulation()
        self.assertIsInstance(simulation, TurtleBotSimulation)
        # Docker availability depends on environment - just check it's a boolean
        self.assertIsInstance(simulation.is_docker_available, bool)
    
    def test_simulation_run_method_exists(self):
        """Test that TurtleBotSimulation has a run method."""
        simulation = TurtleBotSimulation()
        self.assertTrue(hasattr(simulation, 'run'))
        self.assertTrue(callable(getattr(simulation, 'run')))
    
    def test_correct_calling_pattern(self):
        """Test the correct calling pattern: instance.run() not run.run()."""
        simulation = TurtleBotSimulation()
        
        # Mock both Docker SDK and CLI fallback to ensure successful response
        with patch.object(simulation, '_run_with_docker_sdk') as mock_docker, \
             patch.object(simulation, '_run_with_cli_fallback') as mock_cli:
            
            mock_response = SimulationResponse(
                success=True,
                message="Test simulation completed"
            )
            mock_docker.return_value = mock_response
            mock_cli.return_value = mock_response
            
            result = simulation.run(self.simulation_request)
            self.assertIsInstance(result, SimulationResponse)
            self.assertTrue(result.success)
    
    def test_simulation_script_run_function(self):
        """Test that simulation script run function works correctly."""
        # Mock the TurtleBotSimulator to avoid actual simulation
        with patch('simulation.simulation.TurtleBotSimulator') as mock_simulator_class:
            mock_simulator = Mock()
            mock_simulator_class.return_value = mock_simulator
            
            # Call the run function (not run.run())
            run()  # This is the correct pattern
            
            # Verify simulator was created and run was called
            mock_simulator_class.assert_called_once()
            mock_simulator.run.assert_called_once()
    
    def test_docker_sdk_fallback(self):
        """Test Docker SDK fallback to CLI when Docker is unavailable."""
        simulation = TurtleBotSimulation()
        
        # Ensure Docker is marked as unavailable
        simulation.is_docker_available = False
        
        with patch.object(simulation, '_run_with_cli_fallback') as mock_cli:
            mock_cli.return_value = SimulationResponse(
                success=True,
                message="CLI fallback successful"
            )
            
            result = simulation.run(self.simulation_request)
            self.assertTrue(result.success)
            mock_cli.assert_called_once_with(self.simulation_request)
    
    @patch('simulation.main.docker.from_env')
    def test_docker_sdk_integration(self, mock_docker):
        """Test Docker SDK integration when available."""
        # Mock Docker client
        mock_client = Mock()
        mock_docker.return_value = mock_client
        mock_client.ping.return_value = True
        
        # Mock container operations
        mock_container = Mock()
        mock_container.wait.return_value = {'StatusCode': 0}
        mock_container.logs.return_value = b"Simulation completed successfully"
        mock_client.containers.run.return_value = mock_container
        
        # Mock image exists
        mock_client.images.get.return_value = Mock()
        
        simulation = TurtleBotSimulation()
        self.assertTrue(simulation.is_docker_available)
        
        result = simulation.run(self.simulation_request)
        self.assertTrue(result.success)
    
    def test_simulation_response_model(self):
        """Test SimulationResponse model validation."""
        response = SimulationResponse(
            success=True,
            message="Test message",
            output="Test output"
        )
        
        self.assertTrue(response.success)
        self.assertEqual(response.message, "Test message")
        self.assertEqual(response.output, "Test output")
    
    def test_simulation_request_model(self):
        """Test SimulationRequest model validation."""
        request = SimulationRequest(
            script_path="/app/test.py",
            container_name="test-container",
            image_name="test-image:latest",
            parameters={"key": "value"}
        )
        
        self.assertEqual(request.script_path, "/app/test.py")
        self.assertEqual(request.container_name, "test-container")
        self.assertEqual(request.image_name, "test-image:latest")
        self.assertEqual(request.parameters["key"], "value")


class TestFunctionAttributeErrorFixes(unittest.TestCase):
    """Test fixes for 'function' object has no attribute 'run' errors."""
    
    def test_wrong_pattern_demonstration(self):
        """Demonstrate what NOT to do - this would cause the error."""
        # This is what causes the error: run.run() where run is a function
        
        def run_function():
            return "I'm a function"
        
        # This would cause: 'function' object has no attribute 'run'
        # run_function.run()  # ❌ WRONG - don't do this
        
        # Instead, we should just call the function:
        result = run_function()  # ✅ CORRECT
        self.assertEqual(result, "I'm a function")
    
    def test_correct_class_pattern(self):
        """Demonstrate the correct class-based pattern."""
        class SimulationClass:
            def run(self):
                return "Simulation completed"
        
        # Correct pattern: instantiate class, then call method
        sim = SimulationClass()  # Create instance
        result = sim.run()       # ✅ Call method on instance
        
        self.assertEqual(result, "Simulation completed")
    
    def test_turtlebot_simulator_correct_usage(self):
        """Test TurtleBotSimulator uses correct pattern."""
        simulator = TurtleBotSimulator({"max_time": 1})
        
        # This should work - simulator is an instance with run method
        self.assertTrue(hasattr(simulator, 'run'))
        self.assertTrue(callable(getattr(simulator, 'run')))
        
        # Mock the time.sleep to speed up test
        with patch('simulation.simulation.time.sleep'):
            with patch('simulation.simulation.logger'):
                simulator.run()  # ✅ Correct: instance.run()


def run_tests():
    """Run all simulation tests."""
    print("🧪 Running simulation tests...")
    
    # Create test suite
    test_suite = unittest.TestSuite()
    
    # Add test cases
    test_suite.addTest(unittest.makeSuite(TestSimulationFunctionCalling))
    test_suite.addTest(unittest.makeSuite(TestFunctionAttributeErrorFixes))
    
    # Run tests
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(test_suite)
    
    # Return success status
    return result.wasSuccessful()


if __name__ == "__main__":
    success = run_tests()
    sys.exit(0 if success else 1)