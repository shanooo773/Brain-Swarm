FROM osrf/ros:noetic-desktop-full

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV DISPLAY=:1
ENV VNC_RESOLUTION=1024x768
ENV VNC_DEPTH=24
ENV VNC_PASSWORD=gazebo

# Install system dependencies
RUN apt-get update && apt-get install -y \
    # Gazebo and ROS Gazebo integration
    gazebo11 \
    ros-noetic-gazebo-ros \
    ros-noetic-gazebo-ros-pkgs \
    ros-noetic-gazebo-ros-control \
    # VNC server and web interface
    tightvncserver \
    fluxbox \
    supervisor \
    python3-pip \
    # Additional utilities
    wget \
    curl \
    nano \
    x11-utils \
    && rm -rf /var/lib/apt/lists/*

# Install noVNC via pip and git
RUN pip3 install websockify && \
    cd /opt && \
    wget -qO- https://github.com/novnc/noVNC/archive/v1.3.0.tar.gz | tar xz && \
    mv noVNC-1.3.0 noVNC && \
    ln -s /opt/noVNC/vnc.html /opt/noVNC/index.html

# Create directory for VNC
RUN mkdir -p ~/.vnc

# Set VNC password
RUN echo "${VNC_PASSWORD}" | vncpasswd -f > ~/.vnc/passwd && \
    chmod 600 ~/.vnc/passwd

# Create VNC startup script
RUN echo '#!/bin/bash' > ~/.vnc/xstartup && \
    echo 'export XKL_XMODMAP_DISABLE=1' >> ~/.vnc/xstartup && \
    echo 'unset SESSION_MANAGER' >> ~/.vnc/xstartup && \
    echo 'unset DBUS_SESSION_BUS_ADDRESS' >> ~/.vnc/xstartup && \
    echo 'fluxbox &' >> ~/.vnc/xstartup && \
    echo '# Source ROS environment' >> ~/.vnc/xstartup && \
    echo 'source /opt/ros/noetic/setup.bash' >> ~/.vnc/xstartup && \
    echo '# Start Gazebo' >> ~/.vnc/xstartup && \
    echo 'export GAZEBO_MODEL_PATH=/gazebo_workspace/models:/usr/share/gazebo-11/models' >> ~/.vnc/xstartup && \
    echo 'gazebo --verbose' >> ~/.vnc/xstartup

RUN chmod +x ~/.vnc/xstartup

# Create supervisor configuration
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:vnc]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=vncserver :1 -geometry %(ENV_VNC_RESOLUTION)s -depth %(ENV_VNC_DEPTH)s -localhost' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:novnc]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=websockify --web=/opt/noVNC 8080 localhost:5901' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf

# Create directory for Gazebo models and worlds
RUN mkdir -p /gazebo_workspace/models /gazebo_workspace/worlds

# Set Gazebo model path to include custom models
ENV GAZEBO_MODEL_PATH=/gazebo_workspace/models:/usr/share/gazebo-11/models

# Expose VNC and NoVNC ports
EXPOSE 5901 8080

# Set working directory
WORKDIR /gazebo_workspace

# Start supervisor to manage services
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]