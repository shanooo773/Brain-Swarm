# Brain Swarm PHP Conversion - Deployment Guide

## 🎯 Quick Deployment Checklist

### ✅ Pre-Deployment Setup

1. **Download/Clone Project**
   ```bash
   # All files are in the php_version/ directory
   # This is your deployable website
   ```

2. **Hosting Requirements**
   - PHP 7.4+ (recommended: PHP 8.0+)
   - MySQL 5.7+ or MariaDB 10.2+
   - Apache/Nginx with mod_rewrite
   - File upload support
   - 100MB+ disk space

### 📁 File Structure Overview

```
Your hosting public_html/
├── index.php              (Main homepage)
├── sign-in.php           (User login)
├── sign-up.php           (User registration)
├── contact.php           (Contact form)
├── meeting.php           (Meeting scheduler)
├── properties.php        (Our Team page)
├── property-details.php  (Support page)
├── blog/                 (Blog system)
├── admin/                (Admin panel)
├── includes/             (Core functions - PROTECT)
├── templates/            (Templates)
├── static/              (CSS, JS, Images)
├── uploads/             (User uploads - CREATE)
├── .htaccess            (URL routing)
└── README.md            (Documentation)
```

## 🚀 Step-by-Step Deployment

### Step 1: Upload Files

**Method A: FTP/SFTP**
```bash
# Upload entire php_version/ contents to public_html/
# Preserve directory structure
# Total upload time: ~5-10 minutes
```

**Method B: cPanel File Manager**
1. Zip the `php_version/` folder
2. Upload zip to cPanel File Manager
3. Extract in `public_html/`
4. Delete the zip file

### Step 2: Database Setup

1. **Create Database**
   - cPanel → MySQL Databases
   - Database name: `your_prefix_brain_swarm`
   - Create user and assign to database
   - Note: username, password, database name

2. **Import Schema**
   - cPanel → phpMyAdmin
   - Select your database
   - Import → Choose `db.sql`
   - Execute import

### Step 3: Configuration

1. **Copy Sample Config**
   ```bash
   cp config.sample.php includes/config.php
   ```

2. **Edit Database Settings**
   ```php
   // In includes/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Update Site URL**
   ```php
   define('SITE_URL', 'https://yourdomain.com');
   define('SITE_EMAIL', 'admin@yourdomain.com');
   ```

### Step 4: Create Upload Directories

```bash
# Via cPanel File Manager or FTP:
# Create these folders with 755 permissions:

uploads/
uploads/blog_images/
uploads/profile_pics/
```

**Set Permissions:**
- `uploads/` → 755 or 777
- `uploads/blog_images/` → 755 or 777
- `uploads/profile_pics/` → 755 or 777

### Step 5: Test Installation

1. **Visit Your Site**
   - Go to: `https://yourdomain.com`
   - Should see the Brain Swarm homepage

2. **Test Admin Login**
   - Go to: `https://yourdomain.com/sign-in`
   - Username: `admin`
   - Password: `admin123`
   - **CHANGE PASSWORD IMMEDIATELY!**

3. **Test Forms**
   - Contact form: `https://yourdomain.com/contact`
   - Meeting form: `https://yourdomain.com/meeting`

## 🔧 Common Issues & Solutions

### Issue: "Database Connection Failed"
```php
// Check includes/config.php
// Verify database credentials
// Ensure database exists and user has access
```

### Issue: "Page Not Found" / URLs not working
```bash
# Check if .htaccess was uploaded
# Verify mod_rewrite is enabled on your hosting
# Contact hosting support if needed
```

### Issue: "Upload Directory Not Writable"
```bash
# Set folder permissions via cPanel:
# uploads/ → 755 or 777
# uploads/blog_images/ → 755 or 777
# uploads/profile_pics/ → 755 or 777
```

### Issue: "Email Not Sending"
```php
// Option 1: Use PHP mail() function
define('USE_SMTP', false);

// Option 2: Configure SMTP
define('USE_SMTP', true);
// Update SMTP settings in config.php
```

## 🔐 Security Checklist

### ✅ After Deployment:

1. **Change Default Password**
   - Login as admin
   - Change password immediately

2. **Update Configuration**
   - Set `ENVIRONMENT` to 'production'
   - Disable error display

3. **File Permissions**
   - `includes/config.php` → 600 or 644
   - `.htaccess` → 644

4. **SSL Certificate**
   - Enable HTTPS through hosting provider
   - Update `SITE_URL` to use https://

## 📊 Features Test List

### ✅ Test These Features:

- [ ] Homepage loads correctly
- [ ] All navigation links work
- [ ] Contact form submits successfully
- [ ] Meeting form submits successfully
- [ ] User registration works
- [ ] User login works
- [ ] Blog listing shows posts
- [ ] Admin can create blog posts
- [ ] Admin can edit blog posts
- [ ] Admin can delete blog posts
- [ ] Admin dashboard shows statistics
- [ ] File uploads work (blog images)
- [ ] All CSS and images load
- [ ] Mobile responsive design works

## 🎨 Customization

### Colors & Branding
```css
/* Main brand colors are in: */
static/css/templatemo-festava-live.css
static/assets/css/templatemo-villa-agency.css

/* Search for: */
#ff6b6b  /* Primary red color */
#ff8e8e  /* Secondary red color */
```

### Content Updates
```php
// Update site content in:
index.php           // Homepage content
contact.php         // Contact page content  
properties.php      // Team page content
property-details.php // Support page content
```

### Email Templates
```php
// Add email functionality in:
includes/functions.php
// Search for: sendEmail() function
```

## 📞 Support & Maintenance

### Regular Maintenance:
1. **Backup Database** - Weekly via cPanel
2. **Update Blog Content** - Via admin panel
3. **Monitor Form Submissions** - Admin dashboard
4. **Check Error Logs** - cPanel error logs

### Hosting Recommendations:
- **Hostinger Premium** ✅ (Tested)
- **SiteGround** ✅ (Compatible)
- **cPanel-based hosting** ✅ (Compatible)

---

## 🎉 You're Done!

Your Brain Swarm website is now live and fully functional! 

**Admin Panel:** `https://yourdomain.com/admin/`  
**Blog Management:** `https://yourdomain.com/admin/`  
**Form Submissions:** `https://yourdomain.com/admin/forms.php`  

**Remember to:**
1. Change default admin password
2. Test all functionality
3. Set up regular backups
4. Configure email for contact forms

*Happy deploying! 🚀*