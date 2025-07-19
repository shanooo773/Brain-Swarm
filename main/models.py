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


class FormSubmission(models.Model):
    """Model to store all form submissions from the website"""
    FORM_TYPE_CHOICES = [
        ('contact', 'Contact Form'),
        ('meeting', 'Meeting Scheduling Form'),
        ('home', 'Home Page Contact Form'),
    ]
    
    MEETING_PURPOSE_CHOICES = [
        ('buy_kit', 'Buy Robotics Kit'),
        ('custom_project', 'Discuss Custom Project'),
    ]
    
    # Required fields for all forms
    form_type = models.CharField(max_length=20, choices=FORM_TYPE_CHOICES, help_text="Type of form submitted")
    name = models.CharField(max_length=100, help_text="Full name of the submitter")
    email = models.EmailField(help_text="Email address of the submitter")
    message = models.TextField(blank=True, help_text="Message or additional notes")
    submitted_at = models.DateTimeField(auto_now_add=True, help_text="When the form was submitted")
    
    # Optional fields for specific form types
    subject = models.CharField(max_length=200, blank=True, help_text="Subject line (for contact forms)")
    phone = models.CharField(max_length=20, blank=True, help_text="Phone number (for meeting forms)")
    meeting_purpose = models.CharField(max_length=20, choices=MEETING_PURPOSE_CHOICES, blank=True, help_text="Purpose of the meeting")
    preferred_date = models.CharField(max_length=50, blank=True, help_text="Preferred meeting date")
    
    class Meta:
        ordering = ['-submitted_at']
        verbose_name = "Form Submission"
        verbose_name_plural = "Form Submissions"
    
    def __str__(self):
        return f"{self.get_form_type_display()} - {self.name} ({self.submitted_at.strftime('%Y-%m-%d %H:%M')})"
