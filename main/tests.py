from django.test import TestCase
from django.contrib.auth.models import User
from django.db import IntegrityError
from .models import Profile
from .forms import SignUpForm


class ProfileCreationTestCase(TestCase):
    """Test case for Profile creation via signals and forms"""
    
    def test_signal_creates_profile_on_user_creation(self):
        """Test that a Profile is automatically created when a User is created"""
        # Create a user directly (this should trigger the signal)
        user = User.objects.create_user(
            username='testuser',
            email='test@example.com',
            password='testpass123'
        )
        
        # Check that a profile was created
        self.assertTrue(hasattr(user, 'profile'))
        self.assertEqual(user.profile.user, user)
    
    def test_signup_form_creates_user_and_profile(self):
        """Test that SignUpForm creates user and handles profile correctly"""
        form_data = {
            'username': 'newuser',
            'email': 'new@example.com',
            'full_name': 'New User',
            'password1': 'testpass123',
            'password2': 'testpass123'
        }
        
        form = SignUpForm(data=form_data)
        self.assertTrue(form.is_valid())
        
        # Save the form (this might cause duplicate profile creation)
        user = form.save()
        
        # Check that user was created
        self.assertTrue(User.objects.filter(username='newuser').exists())
        
        # Check that only one profile exists for this user
        profiles = Profile.objects.filter(user=user)
        self.assertEqual(profiles.count(), 1)
        
        # Check that profile has the correct data
        profile = profiles.first()
        self.assertEqual(profile.full_name, 'New User')
        self.assertFalse(profile.is_admin)
    
    def test_duplicate_profile_creation_issue(self):
        """Test to reproduce the IntegrityError issue with duplicate profiles"""
        # This test should currently fail due to the duplicate creation issue
        # Create a user first
        user = User.objects.create_user(
            username='dupuser',
            email='dup@example.com',
            password='testpass123'
        )
        
        # At this point, signal should have already created a profile
        self.assertTrue(Profile.objects.filter(user=user).exists())
        
        # Now try to create another profile manually (simulating the form issue)
        # This should raise IntegrityError due to UNIQUE constraint
        with self.assertRaises(IntegrityError):
            Profile.objects.create(user=user, full_name='Duplicate User')
    
    def test_get_or_create_profile_safety(self):
        """Test that get_or_create prevents duplicate profile creation"""
        user = User.objects.create_user(
            username='safeuser',
            email='safe@example.com',
            password='testpass123'
        )
        
        # Signal should have already created a profile
        self.assertTrue(Profile.objects.filter(user=user).exists())
        
        # Try to get_or_create profile multiple times
        profile1, created1 = Profile.objects.get_or_create(user=user)
        profile2, created2 = Profile.objects.get_or_create(user=user)
        
        # Both calls should get the existing profile (signal created it)
        self.assertFalse(created1)  # Signal already created it
        self.assertFalse(created2)  # Should get existing
        self.assertEqual(profile1.id, profile2.id)
        
        # Verify only one profile exists
        self.assertEqual(Profile.objects.filter(user=user).count(), 1)


class EmailValidationTestCase(TestCase):
    """Test case for email uniqueness validation in signup"""
    
    def test_signup_form_prevents_duplicate_email(self):
        """Test that SignUpForm prevents signup with duplicate email"""
        # Create a user first
        User.objects.create_user(
            username='existinguser',
            email='existing@example.com',
            password='testpass123'
        )
        
        # Try to create another user with the same email
        form_data = {
            'username': 'newuser',
            'email': 'existing@example.com',  # Same email
            'full_name': 'New User',
            'password1': 'testpass123',
            'password2': 'testpass123'
        }
        
        form = SignUpForm(data=form_data)
        self.assertFalse(form.is_valid())
        self.assertIn('email', form.errors)
        self.assertEqual(
            form.errors['email'][0], 
            "An account with this email already exists."
        )
    
    def test_signup_form_allows_unique_email(self):
        """Test that SignUpForm allows signup with unique email"""
        form_data = {
            'username': 'uniqueuser',
            'email': 'unique@example.com',
            'full_name': 'Unique User',
            'password1': 'testpass123',
            'password2': 'testpass123'
        }
        
        form = SignUpForm(data=form_data)
        self.assertTrue(form.is_valid())
        
        # Should be able to save successfully
        user = form.save()
        self.assertEqual(user.email, 'unique@example.com')
        self.assertEqual(user.username, 'uniqueuser')
    
    def test_signup_form_case_insensitive_email_check(self):
        """Test that email validation is case-insensitive"""
        # Create a user with lowercase email
        User.objects.create_user(
            username='existinguser',
            email='test@example.com',
            password='testpass123'
        )
        
        # Try to create another user with uppercase email
        form_data = {
            'username': 'newuser',
            'email': 'TEST@EXAMPLE.COM',  # Different case
            'full_name': 'New User',
            'password1': 'testpass123',
            'password2': 'testpass123'
        }
        
        form = SignUpForm(data=form_data)
        # Django's email field automatically converts to lowercase
        # So this should still trigger our validation
        self.assertFalse(form.is_valid())
        self.assertIn('email', form.errors)


class DefaultAdminTestCase(TestCase):
    """Test case for default admin creation"""
    
    def test_default_admin_creation_command(self):
        """Test that the management command creates default admin correctly"""
        from django.core.management import call_command
        from io import StringIO
        
        # Run the command
        out = StringIO()
        call_command('create_default_admin', stdout=out)
        
        # Check that admin user was created
        admin_user = User.objects.get(email='rb.brain.swarm@gmail.com')
        self.assertEqual(admin_user.username, 'rb.brain.swarm@gmail.com')
        self.assertTrue(admin_user.is_superuser)
        self.assertTrue(admin_user.is_staff)
        
        # Check that profile was created and marked as admin
        self.assertTrue(hasattr(admin_user, 'profile'))
        self.assertTrue(admin_user.profile.is_admin)
        self.assertEqual(admin_user.profile.full_name, 'Brain Swarm Admin')
        
        # Check command output
        self.assertIn('Successfully created admin user', out.getvalue())
    
    def test_default_admin_command_prevents_duplicate(self):
        """Test that the command doesn't create duplicate admin"""
        from django.core.management import call_command
        from io import StringIO
        
        # Create admin user first
        User.objects.create_superuser(
            username='rb.brain.swarm@gmail.com',
            email='rb.brain.swarm@gmail.com',
            password='existingpass'
        )
        
        # Run the command
        out = StringIO()
        call_command('create_default_admin', stdout=out)
        
        # Check that no duplicate was created
        admin_users = User.objects.filter(email='rb.brain.swarm@gmail.com')
        self.assertEqual(admin_users.count(), 1)
        
        # Check command output
        self.assertIn('already exists', out.getvalue())
