from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login, logout
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.http import HttpResponseForbidden
from .models import Blog, Profile
from .forms import SignUpForm, BlogForm, ContactForm, MeetingForm


# Create your views here.

def home(request):
    """Home page with contact form"""
    if request.method == 'POST':
        form = ContactForm(request.POST)
        if form.is_valid():
            form.save(form_type='home')
            messages.success(request, 'Thank you for your message! We will get back to you soon.')
            return redirect('home')
    else:
        form = ContactForm()
    
    return render(request, 'index.html', {'contact_form': form})

def contact(request):
    """Contact page with contact form"""
    if request.method == 'POST':
        form = ContactForm(request.POST)
        if form.is_valid():
            form.save(form_type='contact')
            messages.success(request, 'Thank you for contacting us! We will respond to your inquiry soon.')
            return redirect('contact')
    else:
        form = ContactForm()
    
    return render(request, 'contact.html', {'contact_form': form})

def meeting(request):
    """Meeting scheduling page with meeting form"""
    if request.method == 'POST':
        form = MeetingForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, 'Thank you for scheduling a meeting! We will contact you to confirm the details.')
            return redirect('meeting')
    else:
        form = MeetingForm()
    
    return render(request, 'meeting.html', {'meeting_form': form})

def properties(request):
    return render(request, 'properties.html')

def property_details(request):
    return render(request, 'property-details.html')


# Authentication Views

def sign_up(request):
    """User registration view"""
    if request.method == 'POST':
        form = SignUpForm(request.POST)
        if form.is_valid():
            user = form.save()
            messages.success(request, 'Account created successfully! You can now log in.')
            return redirect('sign_in')
    else:
        form = SignUpForm()
    return render(request, 'auth/sign_up.html', {'form': form})


def sign_in(request):
    """User login view - using Django's built-in authentication"""
    return render(request, 'auth/sign_in.html')


@login_required
def sign_out(request):
    """User logout view"""
    logout(request)
    messages.success(request, 'You have been logged out successfully.')
    return redirect('home')


# Blog Views

def blog_list(request):
    """Public view to display all blog posts"""
    blogs = Blog.objects.all()
    return render(request, 'blog/blog_list.html', {'blogs': blogs})


def blog_detail(request, blog_id):
    """Public view to display a single blog post"""
    blog = get_object_or_404(Blog, id=blog_id)
    return render(request, 'blog/blog_detail.html', {'blog': blog})


def is_admin_user(user):
    """Check if user is authenticated and has admin privileges"""
    return user.is_authenticated and hasattr(user, 'profile') and user.profile.is_admin


@login_required
def blog_create(request):
    """Admin-only view to create new blog posts"""
    # Check if user is admin
    if not is_admin_user(request.user):
        return HttpResponseForbidden("You don't have permission to create blog posts.")
    
    if request.method == 'POST':
        form = BlogForm(request.POST, request.FILES)
        if form.is_valid():
            blog = form.save(commit=False)
            blog.author = request.user
            blog.save()
            messages.success(request, 'Blog post created successfully!')
            return redirect('blog_detail', blog_id=blog.id)
    else:
        form = BlogForm()
    
    return render(request, 'blog/blog_form.html', {'form': form, 'action': 'Create'})


@login_required
def blog_edit(request, blog_id):
    """Admin-only view to edit existing blog posts"""
    # Check if user is admin
    if not is_admin_user(request.user):
        return HttpResponseForbidden("You don't have permission to edit blog posts.")
    
    blog = get_object_or_404(Blog, id=blog_id)
    
    if request.method == 'POST':
        form = BlogForm(request.POST, request.FILES, instance=blog)
        if form.is_valid():
            form.save()
            messages.success(request, 'Blog post updated successfully!')
            return redirect('blog_detail', blog_id=blog.id)
    else:
        form = BlogForm(instance=blog)
    
    return render(request, 'blog/blog_form.html', {'form': form, 'blog': blog, 'action': 'Edit'})


@login_required
def blog_delete(request, blog_id):
    """Admin-only view to delete blog posts"""
    # Check if user is admin
    if not is_admin_user(request.user):
        return HttpResponseForbidden("You don't have permission to delete blog posts.")
    
    blog = get_object_or_404(Blog, id=blog_id)
    
    if request.method == 'POST':
        blog.delete()
        messages.success(request, 'Blog post deleted successfully!')
        return redirect('blog_list')
    
    return render(request, 'blog/blog_confirm_delete.html', {'blog': blog})
