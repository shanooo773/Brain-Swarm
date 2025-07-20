<?php
// Database configuration for Brain Swarm Real Estate Website
// Compatible with Hostinger Premium Web Hosting

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'brain_swarm');
define('DB_USER', 'your_username');    // Replace with your database username
define('DB_PASS', 'your_password');    // Replace with your database password
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost');  // Replace with your domain
define('SITE_NAME', 'Brain Swarm');
define('SITE_EMAIL', 'admin@brainswarm.com');

// Upload directories
define('UPLOAD_DIR', 'uploads/');
define('BLOG_IMAGES_DIR', UPLOAD_DIR . 'blog_images/');
define('EVENT_IMAGES_DIR', 'event/uploads/event_images/');
define('PROFILE_PICS_DIR', UPLOAD_DIR . 'profile_pics/');

// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('ADMIN_EMAIL', 'admin@brainswarm.com');

// Email configuration (for contact forms)
define('SMTP_HOST', 'smtp.gmail.com');     // or your hosting provider's SMTP
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_ENCRYPTION', 'tls');

// Timezone
date_default_timezone_set('UTC');

// Error reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
?>