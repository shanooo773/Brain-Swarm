from django.shortcuts import render

# Create your views here.

def home(request):
    """Home page view."""
    return render(request, 'index.html')

def contact(request):
    """Contact page view."""
    return render(request, 'contact.html')

def meeting(request):
    """Meeting page view."""
    return render(request, 'meeting.html')

def properties(request):
    """Properties page view."""
    return render(request, 'properties.html')

def property_details(request):
    """Property details page view."""
    return render(request, 'property-details.html')
