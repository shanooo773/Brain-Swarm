/**
 * Authentication module for Brain Swarm
 * Handles login, token storage, and API communication with FastAPI backend
 */

// Configuration
const AUTH_CONFIG = {
    API_BASE_URL: 'http://localhost:8001',
    TOKEN_KEY: 'brain_swarm_token',
    USER_KEY: 'brain_swarm_user'
};

// Demo account credentials
const DEMO_ACCOUNTS = {
    user: { email: 'demo@brainswarm.com', password: 'demo123' },
    admin: { email: 'demoadmin@brainswarm.com', password: 'demoadmin123' }
};

/**
 * Authentication API service
 */
class AuthService {
    constructor() {
        this.token = localStorage.getItem(AUTH_CONFIG.TOKEN_KEY);
        this.user = JSON.parse(localStorage.getItem(AUTH_CONFIG.USER_KEY) || 'null');
    }

    /**
     * Sign in with email and password
     */
    async signIn(email, password) {
        try {
            const response = await fetch(`${AUTH_CONFIG.API_BASE_URL}/auth/signin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.detail || 'Login failed');
            }

            const data = await response.json();
            
            // Store token and user data
            this.token = data.access_token;
            this.user = data.user;
            
            localStorage.setItem(AUTH_CONFIG.TOKEN_KEY, this.token);
            localStorage.setItem(AUTH_CONFIG.USER_KEY, JSON.stringify(this.user));
            
            return { success: true, user: this.user, token: this.token };
        } catch (error) {
            console.error('Sign in error:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Sign out user
     */
    signOut() {
        this.token = null;
        this.user = null;
        localStorage.removeItem(AUTH_CONFIG.TOKEN_KEY);
        localStorage.removeItem(AUTH_CONFIG.USER_KEY);
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!this.token && !!this.user;
    }

    /**
     * Check if user is admin
     */
    isAdmin() {
        return this.user && this.user.role === 'admin';
    }

    /**
     * Get current user
     */
    getCurrentUser() {
        return this.user;
    }

    /**
     * Get authorization headers
     */
    getAuthHeaders() {
        return this.token ? { 'Authorization': `Bearer ${this.token}` } : {};
    }

    /**
     * Demo login helper
     */
    async demoLogin(type = 'user') {
        const credentials = DEMO_ACCOUNTS[type];
        if (!credentials) {
            throw new Error('Invalid demo account type');
        }
        return this.signIn(credentials.email, credentials.password);
    }
}

/**
 * UI Controller for sign-in form
 */
class SignInController {
    constructor() {
        this.authService = new AuthService();
        this.form = document.getElementById('signin-form');
        this.emailInput = document.getElementById('email');
        this.passwordInput = document.getElementById('password');
        this.submitButton = document.getElementById('signin-submit');
        this.errorContainer = document.getElementById('error-container');
        this.demoUserButton = document.getElementById('demo-user-btn');
        this.demoAdminButton = document.getElementById('demo-admin-btn');
        
        this.initializeEventListeners();
        this.initializeDemoButtons();
    }

    initializeEventListeners() {
        if (this.form) {
            this.form.addEventListener('submit', this.handleSubmit.bind(this));
        }
    }

    initializeDemoButtons() {
        // Create demo buttons if they don't exist
        if (!this.demoUserButton || !this.demoAdminButton) {
            this.createDemoButtons();
        }

        if (this.demoUserButton) {
            this.demoUserButton.addEventListener('click', () => this.handleDemoLogin('user'));
        }

        if (this.demoAdminButton) {
            this.demoAdminButton.addEventListener('click', () => this.handleDemoLogin('admin'));
        }
    }

    createDemoButtons() {
        const formContainer = document.querySelector('.card-body');
        if (!formContainer) return;

        const demoSection = document.createElement('div');
        demoSection.className = 'text-center mt-3';
        demoSection.innerHTML = `
            <hr>
            <p class="mb-2"><small class="text-muted">Demo Accounts:</small></p>
            <div class="d-grid gap-2">
                <button type="button" id="demo-user-btn" class="btn btn-outline-primary btn-sm">
                    Demo User Login
                </button>
                <button type="button" id="demo-admin-btn" class="btn btn-outline-warning btn-sm">
                    Demo Admin Login
                </button>
            </div>
        `;

        formContainer.appendChild(demoSection);
        
        // Re-get references
        this.demoUserButton = document.getElementById('demo-user-btn');
        this.demoAdminButton = document.getElementById('demo-admin-btn');
    }

    async handleSubmit(event) {
        event.preventDefault();
        
        const email = this.emailInput.value.trim();
        const password = this.passwordInput.value;

        if (!email || !password) {
            this.showError('Please enter both email and password');
            return;
        }

        this.setLoading(true);
        this.clearError();

        const result = await this.authService.signIn(email, password);

        if (result.success) {
            this.showSuccess(`Welcome back, ${result.user.full_name || result.user.username}!`);
            this.redirectAfterLogin(result.user);
        } else {
            this.showError(result.error);
        }

        this.setLoading(false);
    }

    async handleDemoLogin(type) {
        this.setLoading(true);
        this.clearError();

        try {
            const result = await this.authService.demoLogin(type);
            
            if (result.success) {
                this.showSuccess(`Demo ${type} login successful!`);
                this.redirectAfterLogin(result.user);
            } else {
                this.showError(result.error);
            }
        } catch (error) {
            this.showError('Demo login failed: ' + error.message);
        }

        this.setLoading(false);
    }

    redirectAfterLogin(user) {
        // Check for redirect URL in query params
        const urlParams = new URLSearchParams(window.location.search);
        const redirectUrl = urlParams.get('next');
        
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else if (user.role === 'admin') {
            // Redirect admin users to admin dashboard
            window.location.href = '/admin/dashboard/';
        } else {
            // Redirect regular users to home
            window.location.href = '/';
        }
    }

    setLoading(loading) {
        if (this.submitButton) {
            this.submitButton.disabled = loading;
            this.submitButton.innerHTML = loading ? 
                '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...' : 
                'Sign In';
        }

        if (this.demoUserButton) this.demoUserButton.disabled = loading;
        if (this.demoAdminButton) this.demoAdminButton.disabled = loading;
    }

    showError(message) {
        if (this.errorContainer) {
            this.errorContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    }

    showSuccess(message) {
        if (this.errorContainer) {
            this.errorContainer.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    }

    clearError() {
        if (this.errorContainer) {
            this.errorContainer.innerHTML = '';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the sign-in page
    if (document.getElementById('signin-form')) {
        new SignInController();
    }
});

// Export for use in other modules
window.AuthService = AuthService;
window.SignInController = SignInController;