<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Admin Dashboard - Brain Swarm';

// Get statistics
$db = Database::getInstance();

$stats = [
    'total_events' => $db->fetch("SELECT COUNT(*) as count FROM event")['count'],
    'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
    'total_submissions' => $db->fetch("SELECT COUNT(*) as count FROM form_submissions")['count'],
    'recent_submissions' => $db->fetch("SELECT COUNT(*) as count FROM form_submissions WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count']
];

// Get recent events
$recent_events = $db->fetchAll(
    "SELECT e.*, u.username as author_username 
     FROM event e 
     LEFT JOIN users u ON e.author_id = u.id 
     ORDER BY e.created_at DESC 
     LIMIT 5"
);

// Get recent form submissions
$recent_forms = $db->fetchAll(
    "SELECT * FROM form_submissions 
     ORDER BY submitted_at DESC 
     LIMIT 5"
);

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">Admin Dashboard</h1>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_events']; ?></h4>
                            <p class="card-text">Total Event Posts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_users']; ?></h4>
                            <p class="card-text">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_submissions']; ?></h4>
                            <p class="card-text">Form Submissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-envelope" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['recent_submissions']; ?></h4>
                            <p class="card-text">This Week</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg"></i> Create Event Post
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo smartUrl('admin/events.php'); ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-journal-text"></i> Manage Events
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo smartUrl('admin/forms.php'); ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-envelope"></i> View Submissions
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo smartUrl('admin/users.php'); ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Event Posts -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Event Posts</h5>
                    <a href="<?php echo smartUrl('admin/events.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_events)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_events as $event): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?php echo htmlspecialchars($event['title']); ?></div>
                                        <small class="text-muted">
                                            By <?php echo htmlspecialchars($event['author_username']); ?> • 
                                            <?php echo formatDate($event['created_at'], 'M d, Y'); ?>
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo smartUrl('event/detail.php?id=' . $event['id']); ?>" 
                                           class="btn btn-outline-primary btn-sm">View</a>
                                        <a href="<?php echo smartUrl('event/edit.php?id=' . $event['id']); ?>" 
                                           class="btn btn-outline-warning btn-sm">Edit</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No events yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Form Submissions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Form Submissions</h5>
                    <a href="<?php echo smartUrl('admin/forms.php'); ?>" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_forms)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_forms as $form): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($form['name']); ?>
                                            <span class="badge bg-<?php echo $form['form_type'] === 'contact' ? 'primary' : ($form['form_type'] === 'meeting' ? 'success' : 'info'); ?>">
                                                <?php echo ucfirst($form['form_type']); ?>
                                            </span>
                                        </h6>
                                        <small class="text-muted"><?php echo formatDate($form['submitted_at'], 'M d, H:i'); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($form['email']); ?></p>
                                    <?php if (!empty($form['subject'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($form['subject']); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No form submissions yet.</p>
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

.bg-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #f0f0f0;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>