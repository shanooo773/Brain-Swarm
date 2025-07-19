from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone


class Profile(models.Model):
    """User profile model linked to Django's built-in User model"""
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    full_name = models.CharField(max_length=100, blank=True, null=True)
    profile_picture = models.ImageField(upload_to='profile_pics/', blank=True, null=True)
    is_admin = models.BooleanField(default=False, help_text="Designates whether the user can manage blogs")
    
    def __str__(self):
        return f"{self.user.username}'s Profile"


class Blog(models.Model):
    """Blog post model"""
    title = models.CharField(max_length=200)
    content = models.TextField()
    author = models.ForeignKey(User, on_delete=models.CASCADE, related_name='blog_posts')
    image = models.ImageField(upload_to='blog_images/', blank=True, null=True, help_text="Optional featured image for the blog post")
    publish_date = models.DateTimeField(default=timezone.now)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    class Meta:
        ordering = ['-publish_date']
    
    def __str__(self):
        return self.title


class Contributor(models.Model):
    """Contributor model for blog posts"""
    blog = models.ForeignKey(Blog, on_delete=models.CASCADE, related_name='contributors')
    name = models.CharField(max_length=100, help_text="Contributor's full name")
    email = models.EmailField(blank=True, null=True, help_text="Optional email address")
    github = models.URLField(blank=True, null=True, help_text="Optional GitHub profile URL")
    linkedin = models.URLField(blank=True, null=True, help_text="Optional LinkedIn profile URL")
    
    class Meta:
        ordering = ['name']
    
    def __str__(self):
        return f"{self.name} - {self.blog.title}"
