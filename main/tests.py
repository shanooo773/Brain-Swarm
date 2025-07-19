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
