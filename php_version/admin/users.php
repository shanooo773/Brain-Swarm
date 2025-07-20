<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Manage Users - Brain Swarm Admin';

// Handle user actions
if ($_POST) {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $action = $_POST['action'];
        
        try {
            $db = Database::getInstance();
            
            if ($action === 'toggle_active') {
                $db->query("UPDATE users SET is_active = NOT is_active WHERE id = ?", [$user_id]);
                setFlashMessage('success', 'User status updated successfully.');
            } elseif ($action === 'delete' && $user_id != SessionManager::get('user_id')) {
                // Don't allow deleting self
                $db->query("DELETE FROM users WHERE id = ?", [$user_id]);
                setFlashMessage('success', 'User deleted successfully.');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
        }
        
        redirect(smartUrl('admin/users.php'));
    }
}

// Get all users
$db = Database::getInstance();
$users = $db->fetchAll(
    "SELECT u.*, p.full_name, p.is_admin 
     FROM users u 
     LEFT JOIN profiles p ON u.id = p.user_id 
     ORDER BY u.date_joined DESC"
);

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5">Manage Users</h1>
                <a href="<?php echo smartUrl('admin/index.php'); ?>" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users (<?php echo count($users); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['full_name'] ?: '-'); ?></td>
                                            <td>
                                                <?php if ($user['is_superuser']): ?>
                                                    <span class="badge bg-danger">Super Admin</span>
                                                <?php elseif ($user['is_admin']): ?>
                                                    <span class="badge bg-warning">Admin</span>
                                                <?php elseif ($user['is_staff']): ?>
                                                    <span class="badge bg-info">Staff</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">User</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatDate($user['date_joined'], 'M d, Y'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($user['id'] != SessionManager::get('user_id')): ?>
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <input type="hidden" name="action" value="toggle_active">
                                                            <button type="submit" class="btn btn-outline-warning" 
                                                                    onclick="return confirm('Toggle user status?')">
                                                                <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button type="submit" class="btn btn-outline-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">Current User</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No users found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>