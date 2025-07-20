<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Manage Contributors - Brain Swarm Admin';

// Handle contributor actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    try {
        $db = Database::getInstance();
        
        if ($action === 'create') {
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $github = sanitizeInput($_POST['github'] ?? '');
            $linkedin = sanitizeInput($_POST['linkedin'] ?? '');
            $event_id = intval($_POST['event_id'] ?? 0);
            
            // Validate required fields
            if (empty($name)) {
                setFlashMessage('error', 'Name is required.');
            } elseif ($event_id <= 0) {
                setFlashMessage('error', 'Please select a valid event.');
            } else {
                // Create contributor
                $db->query(
                    "INSERT INTO contributors (event_id, name, email, github, linkedin) VALUES (?, ?, ?, ?, ?)",
                    [$event_id, $name, $email, $github, $linkedin]
                );
                setFlashMessage('success', 'Contributor added successfully.');
            }
            
        } elseif ($action === 'update' && isset($_POST['contributor_id'])) {
            $contributor_id = intval($_POST['contributor_id']);
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $github = sanitizeInput($_POST['github'] ?? '');
            $linkedin = sanitizeInput($_POST['linkedin'] ?? '');
            $event_id = intval($_POST['event_id'] ?? 0);
            
            // Validate required fields
            if (empty($name)) {
                setFlashMessage('error', 'Name is required.');
            } elseif ($event_id <= 0) {
                setFlashMessage('error', 'Please select a valid event.');
            } else {
                // Update contributor
                $db->query(
                    "UPDATE contributors SET event_id = ?, name = ?, email = ?, github = ?, linkedin = ? WHERE id = ?",
                    [$event_id, $name, $email, $github, $linkedin, $contributor_id]
                );
                setFlashMessage('success', 'Contributor updated successfully.');
            }
            
        } elseif ($action === 'delete' && isset($_POST['contributor_id'])) {
            $contributor_id = intval($_POST['contributor_id']);
            $db->query("DELETE FROM contributors WHERE id = ?", [$contributor_id]);
            setFlashMessage('success', 'Contributor deleted successfully.');
        }
        
    } catch (Exception $e) {
        setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
    }
    
    redirect(smartUrl('admin/contributors.php'));
}

// Get all contributors with event information
$db = Database::getInstance();
$contributors = $db->fetchAll(
    "SELECT c.*, e.title as event_title 
     FROM contributors c 
     LEFT JOIN event e ON c.event_id = e.id 
     ORDER BY c.created_at DESC"
);

// Get all events for the dropdown
$events = $db->fetchAll("SELECT id, title FROM event ORDER BY title");

// Get contributor for editing (if edit mode)
$edit_contributor = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_contributor = $db->fetch("SELECT * FROM contributors WHERE id = ?", [$edit_id]);
}

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5">Manage Contributors</h1>
                <a href="<?php echo smartUrl('admin/index.php'); ?>" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Contributor Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $edit_contributor ? 'Edit Contributor' : 'Add New Contributor'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="action" value="<?php echo $edit_contributor ? 'update' : 'create'; ?>">
                        <?php if ($edit_contributor): ?>
                            <input type="hidden" name="contributor_id" value="<?php echo $edit_contributor['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_id" class="form-label">Event *</label>
                                <select class="form-select" id="event_id" name="event_id" required>
                                    <option value="">Select an event...</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?php echo $event['id']; ?>" 
                                                <?php echo ($edit_contributor && $edit_contributor['event_id'] == $event['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($event['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo $edit_contributor ? htmlspecialchars($edit_contributor['name']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo $edit_contributor ? htmlspecialchars($edit_contributor['email']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="github" class="form-label">GitHub URL</label>
                                <input type="url" class="form-control" id="github" name="github" 
                                       value="<?php echo $edit_contributor ? htmlspecialchars($edit_contributor['github']) : ''; ?>" 
                                       placeholder="https://github.com/username">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                       value="<?php echo $edit_contributor ? htmlspecialchars($edit_contributor['linkedin']) : ''; ?>" 
                                       placeholder="https://linkedin.com/in/username">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_contributor ? 'Update Contributor' : 'Add Contributor'; ?>
                            </button>
                            <?php if ($edit_contributor): ?>
                                <a href="<?php echo smartUrl('admin/contributors.php'); ?>" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contributors List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Contributors (<?php echo count($contributors); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($contributors)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Event</th>
                                        <th>Email</th>
                                        <th>GitHub</th>
                                        <th>LinkedIn</th>
                                        <th>Added</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contributors as $contributor): ?>
                                        <tr>
                                            <td><?php echo $contributor['id']; ?></td>
                                            <td><?php echo htmlspecialchars($contributor['name']); ?></td>
                                            <td>
                                                <a href="<?php echo smartUrl('event/detail.php?id=' . $contributor['event_id']); ?>" 
                                                   class="text-decoration-none" target="_blank">
                                                    <?php echo htmlspecialchars($contributor['event_title'] ?: 'Deleted Event'); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($contributor['email'])): ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($contributor['email']); ?>" 
                                                       class="text-decoration-none">
                                                        <?php echo htmlspecialchars($contributor['email']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($contributor['github'])): ?>
                                                    <a href="<?php echo htmlspecialchars($contributor['github']); ?>" 
                                                       target="_blank" class="text-decoration-none">
                                                        <i class="bi bi-github"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($contributor['linkedin'])): ?>
                                                    <a href="<?php echo htmlspecialchars($contributor['linkedin']); ?>" 
                                                       target="_blank" class="text-decoration-none">
                                                        <i class="bi bi-linkedin"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatDate($contributor['created_at'], 'M d, Y H:i'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo smartUrl('admin/contributors.php?edit=' . $contributor['id']); ?>" 
                                                       class="btn btn-outline-warning">Edit</a>
                                                    
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="contributor_id" value="<?php echo $contributor['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this contributor?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h4 class="text-muted">No contributors yet</h4>
                            <p class="text-muted">Add your first contributor to get started.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Add custom styles
$extra_css = '
<style>
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 12px 12px 0 0 !important;
}

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>