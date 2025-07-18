from django.shortcuts import render

# Create your views here.

def home(request):
    return render(request, 'index.html')

def contact(request):
    return render(request, 'contact.html')

def meeting(request):
    return render(request, 'meeting.html')

def properties(request):
    return render(request, 'properties.html')

def property_details(request):
    return render(request, 'property-details.html')
