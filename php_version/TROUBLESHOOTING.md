# Brain Swarm - Troubleshooting Guide

## Common Issues and Solutions

### 1. Database Connection Failed
**Error**: `Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`

**Solutions**:
```bash
# For XAMPP (Windows)
1. Start MySQL in XAMPP Control Panel
2. Default MySQL credentials: root / (empty password)

# For Linux/Ubuntu
sudo service mysql start
sudo mysql -u root -p
# Set empty password: ALTER USER 'root'@'localhost' IDENTIFIED BY '';

# For other systems
# Update config.php with your actual MySQL credentials
```

### 2. 404 Not Found on Blog URLs
**Issue**: Blog URLs like `/blog/5` return 404

**Solution**: Ensure Apache mod_rewrite is enabled
```apache
# For XAMPP, edit httpd.conf and uncomment:
LoadModule rewrite_module modules/mod_rewrite.so

# Ensure AllowOverride All in Directory directive
<Directory "C:/xampp/htdocs">
    AllowOverride All
</Directory>
```

### 3. File Upload Issues
**Error**: Upload directory not writable

**Solution**:
```bash
# Linux/Ubuntu
chmod 755 uploads/
chmod 755 uploads/blog_images/
chmod 755 uploads/profile_pics/

# Windows (XAMPP)
# Right-click folder > Properties > Security > Edit > Add write permissions
```

### 4. Session Issues
**Error**: Headers already sent / Session start failed

**Solutions**:
- Ensure no whitespace before `<?php` tags
- Check file encoding (must be UTF-8 without BOM)
- Verify session.save_path is writable

### 5. CSS/JS Not Loading
**Issue**: Styling broken, no JavaScript functionality

**Solutions**:
1. Check static/ directory exists with all assets
2. Verify .htaccess MIME types are correct
3. Check browser console for 404 errors
4. Ensure file permissions allow reading

### 6. Admin Panel Access Denied
**Issue**: Cannot access admin panel even with correct credentials

**Solutions**:
1. Verify admin user exists in database:
   ```sql
   SELECT u.*, p.is_admin FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.email = 'admin@brainswarm.com';
   ```
2. Ensure is_admin = 1 in profiles table
3. Clear browser cookies/session
4. Check admin credentials: admin@brainswarm.com / password

### 7. Contact Form Not Sending
**Issue**: Contact form submissions not sending emails

**Solution**: Update SMTP settings in `config.php`:
```php
define('SMTP_HOST', 'your.smtp.server');
define('SMTP_USERNAME', 'your_email@domain.com');
define('SMTP_PASSWORD', 'your_password');
```

## Quick Diagnostic Commands

### Test Database Connection
```bash
php -r "
try {
    \$pdo = new PDO('mysql:host=localhost;dbname=brain_swarm', 'root', '');
    echo 'Database connection: SUCCESS\n';
} catch (Exception \$e) {
    echo 'Database connection: FAILED - ' . \$e->getMessage() . '\n';
}
"
```

### Check File Permissions
```bash
ls -la uploads/
ls -la uploads/blog_images/
ls -la uploads/profile_pics/
```

### Test mod_rewrite
Create test file: `test_rewrite.php`
```php
<?php echo 'Rewrite test: ' . $_SERVER['REQUEST_URI']; ?>
```
Add to .htaccess: `RewriteRule ^test/?$ test_rewrite.php [L]`
Visit: `/test` (should show rewrite working)

## Performance Tips

### Production Optimizations
1. **Disable debug mode** in config.php:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Enable PHP OPcache** in php.ini:
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   ```

3. **Enable gzip compression** (already in .htaccess)

4. **Use CDN** for static assets in production

### Database Optimizations
1. Add indexes for frequently queried columns
2. Enable MySQL query cache
3. Use prepared statements (already implemented)

## Security Checklist

- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Enable HTTPS in production
- [ ] Set secure file permissions
- [ ] Remove debug files
- [ ] Configure firewall rules
- [ ] Regular security updates

## Contact Support

If issues persist:
1. Check server error logs (Apache error.log, PHP error.log)
2. Enable debug mode temporarily in config.php
3. Use browser developer tools to check for JavaScript errors
4. Verify all requirements are met (PHP 8.0+, MySQL 5.7+, Apache with mod_rewrite)

For further assistance, provide:
- Error messages from logs
- PHP/MySQL versions
- Server configuration details
- Steps to reproduce the issue