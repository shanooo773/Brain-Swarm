from django.contrib import admin
from .models import Profile, Blog, Contributor, FormSubmission


@admin.register(Profile)
class ProfileAdmin(admin.ModelAdmin):
    list_display = ['user', 'full_name', 'is_admin']
    list_filter = ['is_admin']
    search_fields = ['user__username', 'user__email', 'full_name']


class ContributorInline(admin.TabularInline):
    """Inline admin for contributors within blog admin"""
    model = Contributor
    extra = 1
    fields = ['name', 'email', 'github', 'linkedin']


@admin.register(Blog)
class BlogAdmin(admin.ModelAdmin):
    list_display = ['title', 'author', 'image', 'publish_date', 'created_at']
    list_filter = ['publish_date', 'created_at', 'author']
    search_fields = ['title', 'content', 'author__username']
    date_hierarchy = 'publish_date'
    ordering = ['-publish_date']
    inlines = [ContributorInline]
    
    def has_module_permission(self, request):
        """Only allow admin users to access this module"""
        return request.user.is_authenticated and hasattr(request.user, 'profile') and request.user.profile.is_admin


@admin.register(Contributor)
class ContributorAdmin(admin.ModelAdmin):
    list_display = ['name', 'blog', 'email', 'github', 'linkedin']
    list_filter = ['blog']
    search_fields = ['name', 'email', 'blog__title']
    ordering = ['blog', 'name']
    
    def has_module_permission(self, request):
        """Only allow admin users to access this module"""
        return request.user.is_authenticated and hasattr(request.user, 'profile') and request.user.profile.is_admin


@admin.register(FormSubmission)
class FormSubmissionAdmin(admin.ModelAdmin):
    list_display = ['form_type', 'name', 'email', 'subject', 'submitted_at']
    list_filter = ['form_type', 'submitted_at', 'meeting_purpose']
    search_fields = ['name', 'email', 'subject', 'message']
    readonly_fields = ['submitted_at']
    date_hierarchy = 'submitted_at'
    ordering = ['-submitted_at']
    
    fieldsets = (
        ('Basic Information', {
            'fields': ('form_type', 'name', 'email', 'submitted_at')
        }),
        ('Contact Details', {
            'fields': ('subject', 'message'),
            'classes': ('collapse',)
        }),
        ('Meeting Details', {
            'fields': ('phone', 'meeting_purpose', 'preferred_date'),
            'classes': ('collapse',)
        }),
    )
    
    def get_readonly_fields(self, request, obj=None):
        """Make all fields readonly except for staff notes if needed"""
        if obj:  # editing an existing object
            return ['form_type', 'name', 'email', 'subject', 'message', 'phone', 'meeting_purpose', 'preferred_date', 'submitted_at']
        return ['submitted_at']
