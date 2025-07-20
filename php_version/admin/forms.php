<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Form Submissions - Brain Swarm Admin';

// Handle form submission actions
if ($_POST) {
    if (isset($_POST['action']) && isset($_POST['submission_id'])) {
        $submission_id = intval($_POST['submission_id']);
        $action = $_POST['action'];
        
        try {
            $db = Database::getInstance();
            
            if ($action === 'delete') {
                $db->query("DELETE FROM form_submissions WHERE id = ?", [$submission_id]);
                setFlashMessage('success', 'Form submission deleted successfully.');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
        }
        
        redirect('forms.php');
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where_clause = '';
$params = [];

if ($filter !== 'all') {
    $where_clause = 'WHERE form_type = ?';
    $params[] = $filter;
}

// Get all form submissions
$db = Database::getInstance();
$forms = $db->fetchAll(
    "SELECT * FROM form_submissions $where_clause ORDER BY submitted_at DESC",
    $params
);

// Get form type counts
$type_counts = $db->fetchAll(
    "SELECT form_type, COUNT(*) as count FROM form_submissions GROUP BY form_type"
);

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5">Form Submissions</h1>
                <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <!-- Filter tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            All (<?php echo count($forms); ?>)
                        </a>
                        <?php foreach ($type_counts as $type): ?>
                            <a href="?filter=<?php echo $type['form_type']; ?>" 
                               class="btn <?php echo $filter === $type['form_type'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo ucfirst($type['form_type']); ?> (<?php echo $type['count']; ?>)
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php echo $filter === 'all' ? 'All Form Submissions' : ucfirst($filter) . ' Submissions'; ?>
                        (<?php echo count($forms); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($forms)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Phone</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($forms as $form): ?>
                                        <tr>
                                            <td><?php echo $form['id']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $form['form_type'] === 'contact' ? 'primary' : ($form['form_type'] === 'meeting' ? 'success' : 'info'); ?>">
                                                    <?php echo ucfirst($form['form_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($form['name']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($form['email']); ?>">
                                                    <?php echo htmlspecialchars($form['email']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($form['subject'] ?: '-'); ?></td>
                                            <td>
                                                <?php if ($form['phone']): ?>
                                                    <a href="tel:<?php echo htmlspecialchars($form['phone']); ?>">
                                                        <?php echo htmlspecialchars($form['phone']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatDate($form['submitted_at'], 'M d, Y H:i'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailModal<?php echo $form['id']; ?>">
                                                        View
                                                    </button>
                                                    
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="submission_id" value="<?php echo $form['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this submission?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal<?php echo $form['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <?php echo ucfirst($form['form_type']); ?> Submission Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Name:</strong> <?php echo htmlspecialchars($form['name']); ?><br>
                                                                <strong>Email:</strong> <?php echo htmlspecialchars($form['email']); ?><br>
                                                                <?php if ($form['phone']): ?>
                                                                    <strong>Phone:</strong> <?php echo htmlspecialchars($form['phone']); ?><br>
                                                                <?php endif; ?>
                                                                <?php if ($form['subject']): ?>
                                                                    <strong>Subject:</strong> <?php echo htmlspecialchars($form['subject']); ?><br>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Type:</strong> <?php echo ucfirst($form['form_type']); ?><br>
                                                                <strong>Submitted:</strong> <?php echo formatDate($form['submitted_at'], 'M d, Y H:i'); ?><br>
                                                                <?php if ($form['meeting_purpose']): ?>
                                                                    <strong>Meeting Purpose:</strong> <?php echo htmlspecialchars($form['meeting_purpose']); ?><br>
                                                                <?php endif; ?>
                                                                <?php if ($form['preferred_date']): ?>
                                                                    <strong>Preferred Date:</strong> <?php echo htmlspecialchars($form['preferred_date']); ?><br>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <?php if ($form['message']): ?>
                                                            <hr>
                                                            <strong>Message:</strong><br>
                                                            <div class="border p-3 bg-light">
                                                                <?php echo nl2br(htmlspecialchars($form['message'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="mailto:<?php echo htmlspecialchars($form['email']); ?>" class="btn btn-primary">
                                                            Reply via Email
                                                        </a>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h4 class="text-muted">No form submissions yet</h4>
                            <p class="text-muted">When visitors submit forms, they will appear here.</p>
                        </div>
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