# Brain Swarm Django Website - Deployment Instructions

## Local Development
1. Install Django: `pip install django`
2. Run migrations: `python manage.py migrate`
3. Start development server: `python manage.py runserver`

## Production Deployment

### Step 1: Prepare settings
- Set `DEBUG = False` in `real_estate_site/settings.py`
- Update `ALLOWED_HOSTS` with your domain name
- Set environment variables for sensitive data

### Step 2: Collect static files
```bash
python manage.py collectstatic
```

### Step 3: Deploy to hosting provider
- Upload project files to your hosting provider (Hostinger, cPanel, etc.)
- Configure WSGI/ASGI application to point to `real_estate_site.wsgi:application`
- Set document root to the project directory
- Ensure Python 3.8+ is available

## Features
- Fully responsive design preserved from original static site
- Django template inheritance for maintainable code
- Proper static file handling
- Production-ready configuration
- URL routing with named patterns
- Contact form ready for backend integration

## Pages
- Home (/)
- Contact (/contact/)
- Meeting (/meeting/)
- Properties (/properties/) - Our Team
- Property Details (/property-details/) - Support

## Static Files Structure
All original assets preserved in `main/static/`:
- CSS files
- JavaScript files
- Images and icons
- Font files
- Video files
- Vendor libraries (Bootstrap, jQuery)