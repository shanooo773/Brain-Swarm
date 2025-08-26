# Gazebo Simulation Setup

Detailed setup guide for the Gazebo/ROS simulation environment component of Brain Swarm.

## Quick Start

```bash
# Start Gazebo simulation
docker compose up gazebo

# Access via browser
# http://localhost:8080
# Password: gazebo
```

## Configuration

| Setting | Value | Description |
|---------|-------|-------------|
| Web Access | `http://localhost:8080` | NoVNC browser interface |
| VNC Port | `5901` | Direct VNC client access |
| Password | `gazebo` | Default VNC password |
| Resolution | `1024x768` | Default screen resolution |

## Custom Models and Worlds

- **Models**: Place in `./gazebo_data/models/`
- **Worlds**: Place in `./gazebo_data/worlds/`
- **Auto-mount**: Files are automatically available in container

## Troubleshooting

- **Container slow to start**: Allow 2-3 minutes for full initialization
- **GPU acceleration**: Uncomment nvidia-docker lines in docker-compose.yml
- **Memory issues**: Increase shared memory with `shm_size: '4gb'`

For complete system setup, see main README.md

#### Custom VNC Password

To set a custom VNC password, modify the `VNC_PASSWORD` environment variable in `docker-compose.yml`:

```yaml
environment:
  - VNC_PASSWORD=your_secure_password
```

#### Custom Screen Resolution

Change the resolution by updating the `VNC_RESOLUTION` environment variable:

```yaml
environment:
  - VNC_RESOLUTION=1920x1080  # Full HD
  # or
  - VNC_RESOLUTION=1280x720   # HD
```

#### Performance Tuning

For better performance on VPS with limited resources:

```yaml
environment:
  - VNC_RESOLUTION=800x600    # Lower resolution
  - VNC_DEPTH=16              # Lower color depth
deploy:
  resources:
    limits:
      memory: 2G              # Limit memory usage
      cpus: '1'               # Limit CPU usage
```

## Custom Gazebo Worlds and Models

### Directory Structure

The compose file automatically creates and mounts directories for custom content:

```
Brain-Swarm/
├── gazebo_data/
│   ├── models/          # Custom Gazebo models
│   └── worlds/          # Custom Gazebo world files
├── Dockerfile
├── docker-compose.yml
└── README.md
```

### Adding Custom Models

1. **Create the models directory** (if not exists):
   ```bash
   mkdir -p gazebo_data/models
   ```

2. **Place your model directories** in `gazebo_data/models/`:
   ```
   gazebo_data/models/
   ├── my_robot/
   │   ├── model.config
   │   ├── model.sdf
   │   └── meshes/
   └── custom_object/
       ├── model.config
       └── model.sdf
   ```

3. **Restart the container** to load new models:
   ```bash
   docker-compose restart gazebo
   ```

### Adding Custom Worlds

1. **Create the worlds directory** (if not exists):
   ```bash
   mkdir -p gazebo_data/worlds
   ```

2. **Place your .world files** in `gazebo_data/worlds/`:
   ```bash
   # Example: Copy a world file
   cp my_simulation.world gazebo_data/worlds/
   ```

3. **Load the world in Gazebo**:
   - Access Gazebo via browser
   - Go to File → Open World
   - Navigate to `/gazebo_workspace/worlds/`
   - Select your world file

## Management Commands

### View Logs
```bash
# View all service logs
docker-compose logs

# View specific service logs
docker-compose logs gazebo

# Follow logs in real-time
docker-compose logs -f gazebo
```

### Stop and Start Services
```bash
# Stop all services
docker-compose down

# Start services
docker-compose up -d

# Restart specific service
docker-compose restart gazebo
```

### Cleanup
```bash
# Stop and remove containers, networks
docker-compose down

# Also remove volumes (warning: deletes VNC settings)
docker-compose down -v

# Remove built images
docker rmi $(docker images -q "brain-swarm*")
```

## Troubleshooting

### Common Issues

1. **Cannot access NoVNC interface**
   - Check if port 8080 is accessible on your VPS
   - Verify firewall settings
   - Check container logs: `docker-compose logs gazebo`

2. **VNC connection fails**
   - Verify VNC password is correct
   - Check if port 5901 is accessible
   - Try accessing VNC directly with a VNC client

3. **Gazebo doesn't start**
   - Check container logs for errors
   - Verify sufficient system resources (RAM, CPU)
   - Try restarting the container

4. **Custom models not loading**
   - Verify model directory structure
   - Check model.config and model.sdf syntax
   - Restart container after adding models

### Performance Tips

1. **For better performance on VPS:**
   ```yaml
   environment:
     - VNC_RESOLUTION=1024x768  # Lower resolution
     - VNC_DEPTH=16             # Lower color depth
   ```

2. **Enable GPU acceleration** (if nvidia-docker available):
   ```yaml
   runtime: nvidia
   environment:
     - NVIDIA_VISIBLE_DEVICES=all
   ```

3. **Allocate more resources** in docker-compose.yml:
   ```yaml
   deploy:
     resources:
       limits:
         memory: 4G
         cpus: '2'
   ```

## Security Considerations

- Change the default VNC password in production
- Consider using VPN or SSH tunneling for remote access
- Limit port access using firewall rules
- Regular security updates of the base image

## Technical Details

### Architecture
- **Base Image**: `osrf/ros:noetic-desktop-full`
- **VNC Server**: TigerVNC
- **Web Interface**: NoVNC with websockify
- **Desktop Environment**: XFCE4
- **Process Manager**: Supervisor

### Services Running in Container
- VNC Server (port 5901)
- NoVNC WebSocket Proxy (port 8080)
- XFCE4 Desktop Environment
- Gazebo Simulation

### File Structure in Container
```
/root/.vnc/          # VNC configuration
/gazebo_workspace/   # Workspace for models and worlds
├── models/          # Custom models
└── worlds/          # Custom world files
```

## Support

For issues related to:
- **Gazebo**: Check [Gazebo documentation](http://gazebosim.org/documentation)
- **ROS**: Check [ROS documentation](http://wiki.ros.org/)
- **Docker**: Check [Docker documentation](https://docs.docker.com/)

## License

This project is part of the Brain-Swarm robotics platform.