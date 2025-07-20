# Brain Swarm - PHP Real Estate Website

A complete PHP-MySQL conversion of the Django-based Brain Swarm real estate website. Fully deployable on standard PHP hosting providers like Hostinger Premium.

## 🚀 Features

- **Complete Real Estate Website** with blog system and contact forms
- **User Authentication** - Sign up, Sign in, User profiles
- **Blog Management System** - Create, edit, delete blog posts (admin only)
- **Contact Forms** - Contact page and meeting scheduling
- **Admin Panel** - Content management for blogs and form submissions
- **Responsive Design** - All original styling preserved
- **Static Files** - All CSS, JS, images, and videos included
- **Email Integration** - Contact form email notifications
- **Security** - Password hashing, input validation, CSRF protection

## 📁 Project Structure

```
php_version/
├── index.php                 # Home page
├── sign-in.php              # User login
├── sign-up.php              # User registration  
├── sign-out.php             # User logout
├── contact.php              # Contact form
├── meeting.php              # Meeting scheduling
├── properties.php           # Our Team page
├── property-details.php     # Support page
├── blog/
│   ├── list.php            # Blog listing
│   ├── detail.php          # Blog post detail
│   ├── create.php          # Create blog post (admin)
│   ├── edit.php            # Edit blog post (admin)
│   └── delete.php          # Delete blog post (admin)
├── admin/
│   ├── index.php           # Admin dashboard
│   ├── blogs.php           # Blog management
│   ├── forms.php           # Form submissions
│   └── users.php           # User management
├── includes/
│   ├── config.php          # Database & site configuration
│   └── functions.php       # Helper functions & database class
├── templates/
│   └── base.php            # Base template
├── static/                 # All CSS, JS, images, videos
│   ├── assets/            # Main assets folder
│   ├── css/               # Additional CSS folder
│   ├── js/                # JavaScript files
│   ├── images/            # Image files
│   ├── fonts/             # Font files
│   ├── vendor/            # Bootstrap, jQuery
│   └── video/             # Video files
├── uploads/               # User uploaded files
│   ├── blog_images/       # Blog post images
│   └── profile_pics/      # User profile pictures
├── .htaccess             # URL routing & security
└── db.sql                # Database schema & sample data
```

## 🛠 Installation on Hostinger (or any PHP hosting)

### Step 1: Database Setup

1. **Create MySQL Database**
   - Login to your hosting control panel (cPanel/hPanel)
   - Go to **MySQL Databases**
   - Create a new database: `brain_swarm`
   - Create a MySQL user and assign to the database
   - Note down: database name, username, password

2. **Import Database Schema**
   - Go to **phpMyAdmin**
   - Select your `brain_swarm` database
   - Click **Import** tab
   - Upload the `db.sql` file
   - Click **Go** to import

### Step 2: File Upload

1. **Upload Files**
   - Upload all files from `php_version/` to your `public_html` folder
   - Ensure directory structure is preserved
   - Set folder permissions:
     - `uploads/` folder: 755 or 777
     - `uploads/blog_images/`: 755 or 777  
     - `uploads/profile_pics/`: 755 or 777

### Step 3: Configuration

1. **Database Configuration**
   - Edit `includes/config.php`
   - Update database settings:
   ```php
   define('DB_HOST', 'localhost');           // Usually localhost
   define('DB_NAME', 'your_database_name');  // Your database name
   define('DB_USER', 'your_username');       // Your database username
   define('DB_PASS', 'your_password');       // Your database password
   ```

2. **Site URL Configuration**
   ```php
   define('SITE_URL', 'https://yourdomain.com');  // Your domain
   define('SITE_EMAIL', 'admin@yourdomain.com');  // Your email
   ```

3. **Email Configuration (Optional)**
   - For contact form emails, update SMTP settings in `config.php`
   - Or use PHP's built-in `mail()` function

### Step 4: Security

1. **File Permissions**
   - Set `includes/config.php` to 600 or 644
   - Ensure `.htaccess` is uploaded and working

2. **Admin Account**
   - Default admin login: `admin` / `password`
   - **IMPORTANT**: Change password immediately after first login

## 🔧 Local Development Setup

### Requirements
- PHP 7.4+ (recommended: PHP 8.0+)
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx)

### Quick Start
1. Clone/download the repository
2. Set up local web server (XAMPP, WAMP, MAMP, or Laravel Valet)
3. Create MySQL database and import `db.sql`
4. Update `includes/config.php` with local database settings
5. Visit `http://localhost/php_version/` in your browser

## 🎨 Customization

### Styling
- All original CSS preserved in `static/css/` and `static/assets/css/`
- Bootstrap 5 included for responsive design
- Custom styles can be added to individual pages

### Content Management
- **Blog Posts**: Admin users can create, edit, delete blog posts
- **Form Submissions**: View contact and meeting requests in admin panel
- **User Management**: Manage user accounts and admin privileges

### Email Integration
The contact forms can send emails using:
1. **SMTP** (recommended for production) - configure in `config.php`
2. **PHP mail()** function - works on most hosting providers

## 🔐 Default Admin Account

**Username:** `admin`  
**Password:** `admin123` (change immediately!)

## 📝 Features Converted from Django

| Django Feature | PHP Equivalent | Status |
|---------------|----------------|---------|
| User Authentication | Session-based auth | ✅ Complete |
| Blog System | Full CRUD operations | ✅ Complete |
| Contact Forms | Form validation & storage | ✅ Complete |
| Admin Interface | Custom admin panel | ✅ Complete |
| Static Files | Preserved structure | ✅ Complete |
| Database Models | MySQL schema | ✅ Complete |
| URL Routing | .htaccess rules | ✅ Complete |
| Template System | PHP includes | ✅ Complete |

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)  
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

## 📞 Support

For issues with the converted PHP version, please check:

1. **File Permissions** - Ensure upload directories are writable
2. **Database Connection** - Verify config.php settings
3. **PHP Version** - Requires PHP 7.4+
4. **URL Rewriting** - Ensure .htaccess is working

## 🚀 Production Deployment Checklist

- [ ] Update `config.php` with production database settings
- [ ] Change default admin password
- [ ] Set `error_reporting(0)` in production
- [ ] Configure SMTP for email functionality
- [ ] Set up SSL certificate (HTTPS)
- [ ] Test all forms and functionality
- [ ] Set up regular database backups
- [ ] Configure caching headers in .htaccess

---

**Original Django Project:** Brain-Swarm  
**PHP Conversion:** Complete with all features preserved  
**Deployment Ready:** Hostinger Premium Web Hosting Compatible