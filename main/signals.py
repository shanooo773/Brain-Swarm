import logging
from django.db.models.signals import post_save
from django.dispatch import receiver
from django.contrib.auth.models import User
from .models import Profile

# Set up logging for profile creation debugging
logger = logging.getLogger(__name__)


@receiver(post_save, sender=User)
def create_or_update_user_profile(sender, instance, created, **kwargs):
    """
    Create or update user profile when user is created or updated.
    
    Uses get_or_create() to prevent duplicate profile creation and
    handles the case where a profile might already exist.
    """
    if created:
        # Use get_or_create to safely handle potential duplicate creation
        profile, profile_created = Profile.objects.get_or_create(
            user=instance,
            defaults={
                'full_name': '',
                'is_admin': False
            }
        )
        
        # Log profile creation for debugging
        if profile_created:
            logger.info(f"Profile created via signal for user: {instance.username}")
        else:
            logger.warning(f"Profile already existed for user: {instance.username} - signal skipped creation")
    else:
        # Update existing profile if it exists
        if hasattr(instance, 'profile'):
            instance.profile.save()
            logger.debug(f"Profile updated for user: {instance.username}")