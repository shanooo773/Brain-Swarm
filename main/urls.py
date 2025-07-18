from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('contact/', views.contact, name='contact'),
    path('meeting/', views.meeting, name='meeting'),
    path('properties/', views.properties, name='properties'),
    path('property-details/', views.property_details, name='property_details'),
]