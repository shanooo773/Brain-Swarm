<?php
require_once 'includes/functions.php';

$page_title = 'Sign Up - Brain Swarm';

// Handle form submission
$errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $password1 = $_POST['password1'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    
    // Store form data to repopulate on error
    $form_data = compact('username', 'email', 'full_name');
    
    // Validate input
    if ($error = validateRequired($username, 'Username')) $errors[] = $error;
    if ($error = validateRequired($email, 'Email')) $errors[] = $error;
    if ($error = validateEmailFormat($email)) $errors[] = $error;
    if ($error = validateRequired($password1, 'Password')) $errors[] = $error;
    if ($error = validateRequired($password2, 'Confirm Password')) $errors[] = $error;
    
    if ($error = validateLength($username, 3, 30, 'Username')) $errors[] = $error;
    if ($error = validateLength($password1, PASSWORD_MIN_LENGTH, 128, 'Password')) $errors[] = $error;
    
    if ($password1 !== $password2) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            
            // Check if username or email already exists
            $existing = $db->fetch(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                [$username, $email]
            );
            
            if ($existing) {
                $errors[] = 'Username or email already exists.';
            } else {
                // Create user
                $hashedPassword = hashPassword($password1);
                $db->query(
                    "INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)",
                    [$username, $email, $hashedPassword, '', '']
                );
                
                $userId = $db->lastInsertId();
                
                // Create profile
                $db->query(
                    "INSERT INTO profiles (user_id, full_name, is_admin) VALUES (?, ?, ?)",
                    [$userId, $full_name, false]
                );
                
                setFlashMessage('success', 'Account created successfully! You can now log in.');
                redirect(smartUrl('sign-in.php'));
            }
        } catch (Exception $e) {
            $errors[] = 'An error occurred while creating your account. Please try again.';
        }
    }
}

// Start output buffering for content
ob_start();
?>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Create Account</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Enter username" 
                                   value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter your email" 
                                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name (Optional)</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   placeholder="Enter your full name (optional)" 
                                   value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password1" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password1" name="password1" 
                                   placeholder="Enter password" required>
                            <div class="form-text">Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password2" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password2" name="password2" 
                                   placeholder="Confirm password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="<?php echo smartUrl('sign-in.php'); ?>" class="text-decoration-none">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Add custom styles
$extra_css = '
<style>
.form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 12px;
}

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
}

.card {
    border: none;
    border-radius: 12px;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include 'templates/base_enhanced.php';
?>