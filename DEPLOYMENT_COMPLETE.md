# Brain Swarm Website - Production Deployment Guide

## Overview
All core functionality has been tested and is working properly:
- ✅ User authentication (Sign In/Sign Up)
- ✅ Blog system (Create, Read, Update, Delete)
- ✅ Admin panel (User management, Blog management, Form submissions)
- ✅ Database operations
- ✅ Session management

## Production Setup Instructions

### 1. Database Configuration

#### For MySQL/MariaDB (Recommended for Production):
1. Edit `php_version/includes/config.php`
2. Change `define('USE_SQLITE', true);` to `define('USE_SQLITE', false);`
3. Update database credentials:
```php
define('DB_HOST', 'localhost');        // Your database host
define('DB_NAME', 'brain_swarm');      // Your database name  
define('DB_USER', 'your_username');    // Your database username
define('DB_PASS', 'your_password');    // Your database password
```

#### Database Setup:
1. Create a new MySQL database named `brain_swarm`
2. Import the database schema: `mysql -u username -p brain_swarm < db.sql`
3. The default admin credentials will be:
   - Username: `admin`
   - Password: `admin123`
   - **IMPORTANT: Change this password immediately after first login**

### 2. File Permissions
Ensure the following directories are writable:
```bash
chmod 755 php_version/uploads/
chmod 755 php_version/uploads/blog_images/
chmod 755 php_version/uploads/profile_pics/
```

### 3. Web Server Configuration

#### Apache (.htaccess already included):
- Ensure mod_rewrite is enabled
- The .htaccess file in php_version/ handles URL rewriting

#### Nginx:
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 4. Security Configuration

#### Production Settings in config.php:
```php
// Change to production environment
define('ENVIRONMENT', 'production');

// Update site URL
define('SITE_URL', 'https://yourdomain.com');

// Enable security settings
ini_set('session.cookie_secure', 1);     // HTTPS only
ini_set('session.cookie_httponly', 1);   // Prevent XSS
```

#### Additional Security:
1. Remove or secure the `/tmp/` test files
2. Ensure database credentials are not in version control
3. Use HTTPS in production
4. Set up regular database backups

### 5. Email Configuration (Optional)
For contact forms to work, configure SMTP in config.php:
```php
define('USE_SMTP', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_ENCRYPTION', 'tls');
```

## Testing Checklist

Before going live, test these functionalities:

### Authentication:
- [ ] User registration works
- [ ] User login works  
- [ ] Password validation works
- [ ] Session persistence works
- [ ] Logout works

### Blog System:
- [ ] Blog list displays correctly
- [ ] Blog creation works (admin only)
- [ ] Blog editing works
- [ ] Blog deletion works
- [ ] Blog images upload correctly

### Admin Panel:
- [ ] Admin dashboard loads
- [ ] User management works
- [ ] Blog management works
- [ ] Form submissions display
- [ ] Admin-only access enforced

### General:
- [ ] All pages load without errors
- [ ] Contact forms work
- [ ] Email notifications work (if configured)
- [ ] File uploads work
- [ ] Navigation works correctly

## Default Login Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`
- **CRITICAL: Change this password immediately after deployment**

**Test User Account:**
- Username: `testuser`  
- Password: `password123`
- Can be deleted after testing

## Troubleshooting

### Common Issues:

1. **Database Connection Errors:**
   - Check credentials in config.php
   - Ensure database server is running
   - Verify database exists and is accessible

2. **File Upload Issues:**
   - Check directory permissions (755 for directories)
   - Verify upload_max_filesize in php.ini
   - Ensure uploads/ directory exists

3. **Session Issues:**
   - Check session.save_path in php.ini
   - Verify session directory is writable
   - Clear browser cookies if testing locally

4. **404 Errors:**
   - Check .htaccess file exists
   - Verify mod_rewrite is enabled (Apache)
   - Check file paths are correct

### Log Files:
Check these for detailed error information:
- PHP error log (usually in /var/log/apache2/error.log)
- Web server access logs
- Database logs

## Support

The Brain Swarm website is now fully functional with:
- Complete user management system
- Full-featured blog platform
- Comprehensive admin panel
- Contact form system
- File upload capabilities
- Security features

All core functionality has been tested and verified working.