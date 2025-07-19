from django.urls import path
from django.contrib.auth import views as auth_views
from . import views

urlpatterns = [
    # Original URLs
    path('', views.home, name='home'),
    path('contact/', views.contact, name='contact'),
    path('meeting/', views.meeting, name='meeting'),
    path('properties/', views.properties, name='properties'),
    path('property-details/', views.property_details, name='property-details'),
    
    # Authentication URLs
    path('sign-up/', views.sign_up, name='sign_up'),
    path('sign-in/', auth_views.LoginView.as_view(template_name='auth/sign_in.html'), name='sign_in'),
    path('sign-out/', views.sign_out, name='sign_out'),
    
    # Blog URLs
    path('blog/', views.blog_list, name='blog_list'),
    path('blog/<int:blog_id>/', views.blog_detail, name='blog_detail'),
    path('blog/create/', views.blog_create, name='blog_create'),
    path('blog/<int:blog_id>/edit/', views.blog_edit, name='blog_edit'),
    path('blog/<int:blog_id>/delete/', views.blog_delete, name='blog_delete'),
]