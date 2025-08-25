# Gazebo Data Directory

This directory contains custom Gazebo models and worlds that will be mounted into the simulation container.

## Directory Structure

```
gazebo_data/
├── models/     # Custom Gazebo models
└── worlds/     # Custom Gazebo world files
```

## Usage

- Place custom model directories in `models/`
- Place custom `.world` files in `worlds/`
- Restart the container to load new content:
  ```bash
  docker-compose restart gazebo
  ```

## Example Model Structure

```
models/
└── my_robot/
    ├── model.config    # Model configuration
    ├── model.sdf       # Model definition
    └── meshes/         # 3D meshes (optional)
        └── robot.dae
```

## Example World File

Save world files with `.world` extension in the `worlds/` directory. Load them in Gazebo via:
File → Open World → Navigate to `/gazebo_workspace/worlds/`