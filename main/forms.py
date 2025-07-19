from django import forms
from django.contrib.auth.forms import UserCreationForm
from django.contrib.auth.models import User
from .models import Profile, Blog


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
    
    def save(self, commit=True):
        user = super().save(commit=False)
        user.email = self.cleaned_data['email']
        if commit:
            user.save()
            # Create profile for the user
            Profile.objects.create(
                user=user,
                full_name=self.cleaned_data.get('full_name', ''),
                is_admin=False  # New users are not admin by default
            )
        return user


class BlogForm(forms.ModelForm):
    """Form for creating and editing blog posts"""
    class Meta:
        model = Blog
        fields = ('title', 'content')
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter blog title'}),
            'content': forms.Textarea(attrs={'class': 'form-control', 'rows': 10, 'placeholder': 'Write your blog content here...'})
        }