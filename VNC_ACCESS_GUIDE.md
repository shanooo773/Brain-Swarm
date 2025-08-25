# Gazebo VNC Access Guide

This guide covers different ways to access the Gazebo simulation environment.

## 🌐 Method 1: Browser Access (NoVNC)

### Steps:
1. Start the container:
   ```bash
   docker-compose up -d
   ```

2. Wait for services to start (2-3 minutes)

3. Open your browser and go to:
   ```
   http://localhost:8080          # Local testing
   http://your-vps-ip:8080        # Remote VPS
   ```

4. Click "Connect" in the NoVNC interface

5. Enter password: `gazebo`

6. Gazebo should start automatically in the desktop environment

### Troubleshooting NoVNC:
- If page doesn't load: Check if websockify is installed in container
- If connection fails: Verify VNC server is running on port 5901
- If password doesn't work: Check VNC_PASSWORD environment variable

## 🖥️ Method 2: Direct VNC Client

### Steps:
1. Install a VNC client on your local machine:
   - **Windows**: TightVNC Viewer, RealVNC
   - **Mac**: VNC Viewer, Screen Sharing (built-in)
   - **Linux**: `apt install tightvncserver-viewer` or similar

2. Start the container:
   ```bash
   docker-compose up -d
   ```

3. Connect with your VNC client to:
   ```
   localhost:5901             # Local testing
   your-vps-ip:5901          # Remote VPS
   ```

4. Enter password: `gazebo`

5. You'll see the desktop with Gazebo starting

### VNC Client Examples:

#### TightVNC (Windows/Linux):
```
vncviewer localhost:5901
```

#### Built-in VNC (Mac):
1. Open Finder
2. Go to "Connect to Server" (Cmd+K)
3. Enter: `vnc://localhost:5901`

#### Command Line (Linux):
```bash
vncviewer localhost:5901
```

## 🔧 Checking Container Status

### View logs:
```bash
# See all logs
docker-compose logs gazebo

# Follow logs in real-time
docker-compose logs -f gazebo

# Check if services are running
docker-compose ps
```

### Expected log output:
```
gazebo_1  | Starting VNC server...
gazebo_1  | New 'X' desktop is container-name:1
gazebo_1  | Starting NoVNC...
gazebo_1  | Services started! Access NoVNC at http://localhost:8080
gazebo_1  | VNC Password: gazebo
```

## 🚀 Gazebo Usage

Once connected via VNC or NoVNC:

### Starting Gazebo:
- Gazebo should start automatically
- If not, open a terminal and run: `gazebo`

### Loading Custom Worlds:
1. In Gazebo: File → Open World
2. Navigate to: `/gazebo_workspace/worlds/`
3. Select your world file (e.g., `brain_swarm_demo.world`)

### Adding Models:
1. Custom models are available in: `/gazebo_workspace/models/`
2. Use the "Insert" tab in Gazebo to add models
3. Models from `/gazebo_workspace/models/` will appear in the model list

## 🛠️ Port Forwarding for VPS

If running on a VPS, ensure ports are open:

### UFW (Ubuntu):
```bash
sudo ufw allow 8080/tcp
sudo ufw allow 5901/tcp
```

### iptables:
```bash
sudo iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 5901 -j ACCEPT
```

### Cloud Provider Security Groups:
Add inbound rules for:
- Port 8080 (TCP) - NoVNC
- Port 5901 (TCP) - VNC

## 🔒 Security Considerations

### Change Default Password:
Edit `docker-compose.yml`:
```yaml
environment:
  - VNC_PASSWORD=your_secure_password
```

### Secure Access:
1. **VPN**: Use VPN for remote access instead of exposing ports
2. **SSH Tunnel**: Forward ports through SSH:
   ```bash
   ssh -L 8080:localhost:8080 -L 5901:localhost:5901 user@your-vps
   ```
3. **Firewall**: Restrict access to specific IP addresses

## 📊 Performance Optimization

### For Low-Resource VPS:
```yaml
environment:
  - VNC_RESOLUTION=800x600
  - VNC_DEPTH=16
deploy:
  resources:
    limits:
      memory: 2G
      cpus: '1'
```

### For High-Performance:
```yaml
environment:
  - VNC_RESOLUTION=1920x1080
  - VNC_DEPTH=24
# Enable GPU if available:
# runtime: nvidia
```

## ❓ Common Issues

### "Connection refused" error:
- Check if container is running: `docker-compose ps`
- Verify ports are exposed: `docker port gazebo-vnc`
- Check firewall settings

### Gazebo won't start:
- Check container logs: `docker-compose logs gazebo`
- Verify X11 display is available: Check VNC connection first
- Ensure sufficient memory (minimum 2GB recommended)

### Poor performance:
- Reduce VNC resolution and color depth
- Close unnecessary applications in the VNC session
- Increase container resource limits
- Consider using GPU acceleration if available

### VNC password doesn't work:
- Check VNC_PASSWORD in docker-compose.yml
- Restart container: `docker-compose restart gazebo`
- Verify password was set correctly in logs

## 📞 Getting Help

Check logs for specific error messages:
```bash
docker-compose logs gazebo | grep -i error
```

Container shell access for debugging:
```bash
docker-compose exec gazebo /bin/bash
```