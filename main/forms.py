import logging
from django import forms
from django.contrib.auth.forms import UserCreationForm
from django.contrib.auth.models import User
from .models import Profile, Blog, FormSubmission

# Set up logging for form debugging
logger = logging.getLogger(__name__)


class SignUpForm(UserCreationForm):
    """Extended user registration form"""
    email = forms.EmailField(
        required=True,
        widget=forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'Enter your email'})
    )
    full_name = forms.CharField(
        max_length=100, 
        required=False,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter your full name (optional)'})
    )
    
    class Meta:
        model = User
        fields = ('username', 'email', 'full_name', 'password1', 'password2')
    
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.fields['username'].widget.attrs.update({'class': 'form-control', 'placeholder': 'Enter username'})
        self.fields['password1'].widget.attrs.update({'class': 'form-control', 'placeholder': 'Enter password'})
        self.fields['password2'].widget.attrs.update({'class': 'form-control', 'placeholder': 'Confirm password'})
    
    def clean_email(self):
        """Validate that email is unique (case-insensitive)"""
        email = self.cleaned_data.get('email')
        if email and User.objects.filter(email__iexact=email).exists():
            raise forms.ValidationError("An account with this email already exists.")
        return email
    
    def save(self, commit=True):
        """
        Save the user and update the profile created by the signal.
        
        The signal automatically creates a Profile when the User is saved.
        This method updates that profile with additional form data.
        Also handles the case where a user exists but profile is missing.
        """
        user = super().save(commit=False)
        user.email = self.cleaned_data['email']
        
        if commit:
            user.save()
            
            # The signal has already created a Profile, so we just update it
            # with the additional data from the form
            try:
                profile = user.profile
                profile.full_name = self.cleaned_data.get('full_name', '')
                profile.save()
                logger.info(f"Profile updated with form data for user: {user.username}")
            except Profile.DoesNotExist:
                # Fallback: if somehow the signal didn't create a profile, create one
                Profile.objects.create(
                    user=user,
                    full_name=self.cleaned_data.get('full_name', ''),
                    is_admin=False
                )
                logger.warning(f"Profile was missing for user {user.username} - created manually as fallback")
        
        return user


class BlogForm(forms.ModelForm):
    """Form for creating and editing blog posts"""
    class Meta:
        model = Blog
        fields = ('title', 'content', 'image')
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter blog title'}),
            'content': forms.Textarea(attrs={'class': 'form-control', 'rows': 10, 'placeholder': 'Write your blog content here...'}),
            'image': forms.FileInput(attrs={'class': 'form-control', 'accept': 'image/*'})
        }


class ContactForm(forms.ModelForm):
    """Form for contact page and home page contact forms"""
    class Meta:
        model = FormSubmission
        fields = ('name', 'email', 'subject', 'message')
        widgets = {
            'name': forms.TextInput(attrs={
                'class': 'form-control',
                'placeholder': 'Your Name...',
                'required': True,
                'autocomplete': 'on'
            }),
            'email': forms.EmailInput(attrs={
                'class': 'form-control', 
                'placeholder': 'Your E-mail...',
                'required': True,
                'pattern': '[^ @]*@[^ @]*'
            }),
            'subject': forms.TextInput(attrs={
                'class': 'form-control',
                'placeholder': 'Subject...',
                'autocomplete': 'on'
            }),
            'message': forms.Textarea(attrs={
                'class': 'form-control',
                'placeholder': 'Your Message',
                'rows': 5
            })
        }
    
    def save(self, commit=True, form_type='contact'):
        """Save the form with the specified form type"""
        instance = super().save(commit=False)
        instance.form_type = form_type
        if commit:
            instance.save()
        return instance


class MeetingForm(forms.ModelForm):
    """Form for meeting scheduling"""
    class Meta:
        model = FormSubmission
        fields = ('name', 'email', 'phone', 'meeting_purpose', 'preferred_date', 'message')
        widgets = {
            'name': forms.TextInput(attrs={
                'class': 'form-control',
                'placeholder': 'Your Full Name',
                'required': True
            }),
            'email': forms.EmailInput(attrs={
                'class': 'form-control',
                'placeholder': 'Your Email Address',
                'required': True,
                'pattern': '[^ @]*@[^ @]*'
            }),
            'phone': forms.TextInput(attrs={
                'class': 'form-control',
                'placeholder': 'Phone (e.g. 0300-123-4567)',
                'pattern': '[0-9]{4}-[0-9]{3}-[0-9]{4}',
                'required': True
            }),
            'meeting_purpose': forms.RadioSelect(attrs={
                'class': 'form-check-input'
            }),
            'preferred_date': forms.TextInput(attrs={
                'class': 'form-control',
                'placeholder': 'Preferred Meeting Date (e.g. 2025-07-12)',
                'required': True
            }),
            'message': forms.Textarea(attrs={
                'class': 'form-control',
                'placeholder': 'Any specific requirements or notes?',
                'rows': 3
            })
        }
    
    def save(self, commit=True):
        """Save the form with meeting form type"""
        instance = super().save(commit=False)
        instance.form_type = 'meeting'
        if commit:
            instance.save()
        return instance