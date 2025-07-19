from django.contrib import admin
from .models import Profile, Blog


@admin.register(Profile)
class ProfileAdmin(admin.ModelAdmin):
    list_display = ['user', 'full_name', 'is_admin']
    list_filter = ['is_admin']
    search_fields = ['user__username', 'user__email', 'full_name']


@admin.register(Blog)
class BlogAdmin(admin.ModelAdmin):
    list_display = ['title', 'author', 'publish_date', 'created_at']
    list_filter = ['publish_date', 'created_at', 'author']
    search_fields = ['title', 'content', 'author__username']
    date_hierarchy = 'publish_date'
    ordering = ['-publish_date']
