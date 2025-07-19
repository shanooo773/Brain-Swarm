from django.core.management.base import BaseCommand
from django.contrib.auth.models import User
from main.models import Profile


class Command(BaseCommand):
    help = 'Create default admin user for Brain Swarm'

    def handle(self, *args, **options):
        username = 'rb.brain.swarm@gmail.com'
        email = 'rb.brain.swarm@gmail.com'
        password = 'Admin4362823'
        
        # Check if user already exists
        if User.objects.filter(email=email).exists():
            self.stdout.write(
                self.style.WARNING(f'User with email {email} already exists')
            )
            return
        
        # Create superuser
        user = User.objects.create_superuser(
            username=username,
            email=email,
            password=password
        )
        
        # Update profile to be admin
        try:
            profile = user.profile
            profile.is_admin = True
            profile.full_name = 'Brain Swarm Admin'
            profile.save()
            self.stdout.write(
                self.style.SUCCESS(f'Successfully created admin user: {username}')
            )
        except Profile.DoesNotExist:
            # Create profile if signal didn't create it
            Profile.objects.create(
                user=user,
                full_name='Brain Swarm Admin',
                is_admin=True
            )
            self.stdout.write(
                self.style.SUCCESS(f'Successfully created admin user and profile: {username}')
            )