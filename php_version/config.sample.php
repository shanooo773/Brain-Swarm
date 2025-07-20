<?php
// Sample configuration file for Brain Swarm Real Estate Website
// Copy this file to includes/config.php and update with your settings

// Database configuration
define('DB_HOST', 'localhost');                    // Database host (usually localhost)
define('DB_NAME', 'brain_swarm');                 // Your database name
define('DB_USER', 'your_db_username');            // Your database username
define('DB_PASS', 'your_db_password');            // Your database password
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost');           // Your domain URL (no trailing slash)
define('SITE_NAME', 'Brain Swarm');
define('SITE_EMAIL', 'admin@brainswarm.com');     // Site admin email

// Upload directories (relative to site root)
define('UPLOAD_DIR', 'uploads/');
define('BLOG_IMAGES_DIR', UPLOAD_DIR . 'blog_images/');
define('EVENT_IMAGES_DIR', UPLOAD_DIR . 'event_images/');
define('PROFILE_PICS_DIR', UPLOAD_DIR . 'profile_pics/');

// Security settings
define('SESSION_LIFETIME', 3600);                 // Session timeout (1 hour)
define('PASSWORD_MIN_LENGTH', 8);                 // Minimum password length
define('ADMIN_EMAIL', 'admin@brainswarm.com');

// Email configuration for contact forms
// Option 1: SMTP (recommended for production)
define('SMTP_HOST', 'smtp.gmail.com');            // SMTP server
define('SMTP_PORT', 587);                         // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_USERNAME', 'your_email@gmail.com');  // SMTP username
define('SMTP_PASSWORD', 'your_app_password');     // SMTP password (use app passwords for Gmail)
define('SMTP_ENCRYPTION', 'tls');                 // Encryption: 'tls' or 'ssl'

// Option 2: PHP mail() function (simpler, works on most hosting)
define('USE_SMTP', false);                        // Set to true to use SMTP, false for PHP mail()

// Timezone
date_default_timezone_set('UTC');                 // Change to your timezone

// Environment settings
define('ENVIRONMENT', 'development');             // 'development' or 'production'

// Error reporting (set to 0 for production)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
ini_set('session.cookie_httponly', 1);

// File upload settings
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', 300);

/*
===========================================
INSTALLATION INSTRUCTIONS:
===========================================

1. COPY THIS FILE:
   - Copy this file to: includes/config.php

2. UPDATE DATABASE SETTINGS:
   - DB_HOST: Usually 'localhost'
   - DB_NAME: Your database name
   - DB_USER: Your database username  
   - DB_PASS: Your database password

3. UPDATE SITE SETTINGS:
   - SITE_URL: Your website URL (https://yourdomain.com)
   - SITE_EMAIL: Your admin email address

4. EMAIL CONFIGURATION:
   - For SMTP: Update SMTP settings with your email provider details
   - For simple setup: Set USE_SMTP to false

5. ENVIRONMENT:
   - Set ENVIRONMENT to 'production' for live sites

6. CREATE UPLOAD DIRECTORIES:
   - Create: uploads/blog_images/ (permissions 755)
   - Create: uploads/event_images/ (permissions 755)
   - Create: uploads/profile_pics/ (permissions 755)

7. IMPORT DATABASE:
   - Import db.sql into your MySQL database

8. DEFAULT ADMIN LOGIN:
   - Username: admin
   - Password: admin123
   - CHANGE IMMEDIATELY after first login!

===========================================
HOSTING PROVIDER SPECIFIC NOTES:
===========================================

HOSTINGER:
- DB_HOST is usually 'localhost'
- Use their database credentials from control panel
- Enable mod_rewrite for pretty URLs

CPANEL HOSTING:
- DB_HOST might be 'localhost' or specific server
- Check hosting provider documentation

SHARED HOSTING:
- Some providers require specific SMTP settings
- Contact support if email doesn't work

===========================================
*/
?>