# Brain Swarm PHP Blog System - Setup and Testing Guide

## 🔧 Issues Fixed

### ✅ 1. Blog "Read More" URL Issue - RESOLVED
- **Root Cause**: The system correctly uses `blog/detail.php?id=X` (not `read.php`)
- **Solution**: Created `blog/read.php` for backward compatibility that redirects to `detail.php`
- **URLs that work**:
  - `/blog/detail.php?id=5` (direct access)
  - `/blog/5` (pretty URL via .htaccess)
  - `/blog/read.php?id=5` (backward compatibility redirect)

### ✅ 2. Database Connection - ALREADY CORRECT
- **Database**: Correctly configured for MySQL (not SQLite)
- **Connection**: `mysql:host=localhost;dbname=brain_swarm;charset=utf8mb4`
- **Credentials**: `root` user with empty password (development default)

### ✅ 3. Sign In/Sign Up Authentication - WORKING
- **Password Security**: Uses `password_hash()` and `password_verify()`
- **Validation**: Proper form validation and error handling
- **Sessions**: Secure session management implemented

### ✅ 4. Admin Panel Protection - WORKING
- **Access Control**: Protected by `requireAdmin()` function
- **User Roles**: Uses `is_admin` field in profiles table
- **Default Admin**: 
  - **Username**: `admin`
  - **Email**: `admin@brainswarm.com`
  - **Password**: `password`

### ✅ 5. Upload System - FIXED
- **Directories Created**: 
  - `uploads/` (main upload directory)
  - `uploads/blog_images/` (blog post images)
  - `uploads/profile_pics/` (user profile pictures)
- **Security**: Added `.htaccess` to prevent PHP execution in uploads
- **Permissions**: All directories are writable

## 🛠️ Setup Instructions

### 1. Database Setup
```sql
-- Create database
CREATE DATABASE IF NOT EXISTS brain_swarm;

-- Import schema (run from project root)
mysql -u root -p brain_swarm < db.sql
```

### 2. XAMPP Setup
1. Copy `php_version/` folder to `C:\xampp\htdocs\`
2. Rename to your preferred folder name (e.g., `brain_swarm`)
3. Ensure Apache and MySQL are running
4. Access via `http://localhost/brain_swarm/`

### 3. Configuration (Optional)
Edit `includes/config.php` for:
- Site URL (for production)
- SMTP settings (for contact forms)
- Database credentials (if different)

## 🧪 Testing Checklist

### Database & Connection
- [ ] MySQL database `brain_swarm` created
- [ ] All tables imported from `db.sql`
- [ ] PHP can connect to database
- [ ] Sample data loaded (admin user, sample blog post)

### Authentication System
- [ ] Sign up form creates new users
- [ ] Sign in form authenticates users
- [ ] Admin login works with `admin@brainswarm.com` / `password`
- [ ] Sessions persist across pages
- [ ] Sign out properly destroys sessions

### Blog System
- [ ] Blog list page shows all posts
- [ ] "Read More" links work (`/blog/detail.php?id=X`)
- [ ] Pretty URLs work (`/blog/X`)
- [ ] Backward compatibility URLs work (`/blog/read.php?id=X`)
- [ ] Blog images display correctly
- [ ] Pagination works (if implemented)

### Admin Panel
- [ ] Admin dashboard accessible only to admin users
- [ ] Blog creation/editing works
- [ ] User management works
- [ ] Form submissions viewable
- [ ] File uploads work correctly

### File Uploads
- [ ] Upload directories exist and are writable
- [ ] Image uploads work for blog posts
- [ ] Security restrictions prevent PHP upload
- [ ] File size limits enforced

## 🔐 Default Credentials

**Admin Account:**
- Username: `admin`
- Email: `admin@brainswarm.com`
- Password: `password`

**Database:**
- Host: `localhost`
- Database: `brain_swarm`
- Username: `root`
- Password: `` (empty for XAMPP default)

## 🚀 Production Deployment Notes

### Security
1. Change default admin password immediately
2. Update database credentials
3. Enable HTTPS in `.htaccess`
4. Set proper file permissions (644 for files, 755 for directories)
5. Remove or secure debug/test files

### Performance
1. Enable opcache for PHP
2. Configure MySQL query cache
3. Use CDN for static assets
4. Enable gzip compression

### Hosting (Hostinger)
1. Upload files via FTP/cPanel
2. Import database via phpMyAdmin
3. Update `config.php` with hosting database credentials
4. Update `SITE_URL` in config

## 📝 Additional Notes

- All PHP files pass syntax validation
- Pretty URLs configured in `.htaccess` 
- Responsive design with Bootstrap CSS
- Cross-browser compatible
- SEO-friendly URL structure
- XSS and CSRF protection implemented