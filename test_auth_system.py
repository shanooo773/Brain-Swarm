#!/usr/bin/env python3
"""
Test script for Brain Swarm Authentication System
Demonstrates sign-up, sign-in, booking, and admin functionality
"""

import requests
import json
import time
import sys

# Configuration
AUTH_BACKEND_URL = "http://localhost:8001"
DJANGO_FRONTEND_URL = "http://localhost:8000"

def test_auth_backend():
    """Test the FastAPI authentication backend"""
    print("🔐 Testing FastAPI Auth Backend...")
    
    try:
        # Test health endpoint
        response = requests.get(f"{AUTH_BACKEND_URL}/health")
        if response.status_code == 200:
            print("✅ Health check passed")
        else:
            print("❌ Health check failed")
            return False
        
        # Test sign-up
        import random
        random_suffix = random.randint(1000, 9999)
        signup_data = {
            "username": f"testuser{random_suffix}",
            "email": f"test{random_suffix}@example.com",
            "password": "testpass123",
            "full_name": "Test User"
        }
        
        response = requests.post(f"{AUTH_BACKEND_URL}/auth/signup", json=signup_data)
        if response.status_code == 200:
            print("✅ Sign-up successful")
            token_data = response.json()
            access_token = token_data["access_token"]
        else:
            print(f"❌ Sign-up failed: {response.text}")
            return False
        
        # Test sign-in
        signin_data = {
            "email": f"test{random_suffix}@example.com",
            "password": "testpass123"
        }
        
        response = requests.post(f"{AUTH_BACKEND_URL}/auth/signin", json=signin_data)
        if response.status_code == 200:
            print("✅ Sign-in successful")
            token_data = response.json()
            access_token = token_data["access_token"]
        else:
            print(f"❌ Sign-in failed: {response.text}")
            return False
        
        # Test authenticated endpoint
        headers = {"Authorization": f"Bearer {access_token}"}
        response = requests.get(f"{AUTH_BACKEND_URL}/auth/me", headers=headers)
        if response.status_code == 200:
            print("✅ Authenticated request successful")
            user_info = response.json()
            print(f"   User: {user_info['username']} ({user_info['email']})")
        else:
            print("❌ Authenticated request failed")
            return False
        
        # Test booking creation
        booking_data = {
            "service_type": "robotics_kit",
            "preferred_date": "2025-09-01",
            "message": "Test booking",
            "phone": "+1234567890"
        }
        
        response = requests.post(f"{AUTH_BACKEND_URL}/bookings/", json=booking_data, headers=headers)
        if response.status_code == 200:
            print("✅ Booking creation successful")
            booking_info = response.json()
            print(f"   Booking ID: {booking_info['id']}")
        else:
            print(f"❌ Booking creation failed: {response.text}")
            return False
        
        # Test admin login (default admin user)
        admin_signin_data = {
            "email": "admin@brainswarm.com",
            "password": "admin123"
        }
        
        response = requests.post(f"{AUTH_BACKEND_URL}/auth/signin", json=admin_signin_data)
        if response.status_code == 200:
            print("✅ Admin sign-in successful")
            admin_token_data = response.json()
            admin_access_token = admin_token_data["access_token"]
        else:
            print("❌ Admin sign-in failed")
            return False
        
        # Test admin dashboard
        admin_headers = {"Authorization": f"Bearer {admin_access_token}"}
        response = requests.get(f"{AUTH_BACKEND_URL}/admin/stats", headers=admin_headers)
        if response.status_code == 200:
            print("✅ Admin dashboard access successful")
            stats = response.json()
            print(f"   Total users: {stats['total_users']}")
            print(f"   Total bookings: {stats['total_bookings']}")
        else:
            print("❌ Admin dashboard access failed")
            return False
        
        return True
        
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to Auth Backend - is it running?")
        return False
    except Exception as e:
        print(f"❌ Auth Backend test failed: {e}")
        return False

def test_django_frontend():
    """Test the Django frontend"""
    print("\n🌐 Testing Django Frontend...")
    
    try:
        # Test homepage
        response = requests.get(DJANGO_FRONTEND_URL)
        if response.status_code == 200:
            print("✅ Homepage accessible")
        else:
            print("❌ Homepage not accessible")
            return False
        
        # Test sign-in page
        response = requests.get(f"{DJANGO_FRONTEND_URL}/sign-in/")
        if response.status_code == 200:
            print("✅ Sign-in page accessible")
        else:
            print("❌ Sign-in page not accessible")
            return False
        
        # Test sign-up page
        response = requests.get(f"{DJANGO_FRONTEND_URL}/sign-up/")
        if response.status_code == 200:
            print("✅ Sign-up page accessible")
        else:
            print("❌ Sign-up page not accessible")
            return False
        
        return True
        
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to Django Frontend - is it running?")
        return False
    except Exception as e:
        print(f"❌ Django Frontend test failed: {e}")
        return False

def main():
    """Main test function"""
    print("🧪 Brain Swarm System Test")
    print("=" * 40)
    
    # Test Auth Backend
    auth_success = test_auth_backend()
    
    # Test Django Frontend  
    django_success = test_django_frontend()
    
    # Summary
    print("\n📊 Test Summary")
    print("=" * 20)
    print(f"Auth Backend: {'✅ PASS' if auth_success else '❌ FAIL'}")
    print(f"Django Frontend: {'✅ PASS' if django_success else '❌ FAIL'}")
    
    if auth_success and django_success:
        print("\n🎉 All tests passed!")
        print("\n📋 Next steps:")
        print("1. Start both services using setup.sh")
        print("2. Access Auth API at http://localhost:8001/docs")
        print("3. Access Django frontend at http://localhost:8000")
        print("4. Use admin credentials: admin/admin123")
        return 0
    else:
        print("\n❌ Some tests failed!")
        print("\n💡 Make sure both services are running:")
        print("   cd auth_backend && python3 main.py")
        print("   python3 manage.py runserver 8000")
        return 1

if __name__ == "__main__":
    sys.exit(main())