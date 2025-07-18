# Brain-Swarm - Real Estate Django Project

A Django-based real estate website project.

## Setup

1. Install dependencies:
```bash
pip install -r requirements.txt
```

2. Run migrations:
```bash
python manage.py migrate
```

3. Collect static files:
```bash
python manage.py collectstatic
```

4. Run the development server:
```bash
python manage.py runserver
```

## Project Structure

- `real_estate_site/` - Django project configuration
- `main/` - Main Django application with views, models, and templates
- `main/static/` - Static files (CSS, JS, images)
- `main/templates/` - HTML templates