<?php
require_once 'includes/functions.php';

$page_title = 'Sign In - Brain Swarm';

// Handle form submission
$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if ($error = validateRequired($username, 'Username')) $errors[] = $error;
    if ($error = validateRequired($password, 'Password')) $errors[] = $error;
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            $user = $db->fetch(
                "SELECT u.*, p.full_name, p.profile_picture, p.is_admin 
                 FROM users u 
                 LEFT JOIN profiles p ON u.id = p.user_id 
                 WHERE u.username = ? AND u.is_active = 1",
                [$username]
            );
            
            if ($user && verifyPassword($password, $user['password'])) {
                // Update last login
                $db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
                
                // Set session
                SessionManager::set('user_id', $user['id']);
                
                setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
                redirect(url());
            } else {
                $errors[] = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $errors[] = 'An error occurred. Please try again.';
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
                    <h2 class="text-center mb-4">Sign In</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <p>Please correct the errors below:</p>
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
                                   value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? <a href="<?php echo url('sign-up.php'); ?>" class="text-decoration-none">Sign Up</a></p>
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
include 'templates/base.php';
?>