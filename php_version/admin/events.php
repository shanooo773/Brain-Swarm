<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Manage Events - Brain Swarm Admin';

// Handle event actions
if ($_POST) {
    if (isset($_POST['action']) && isset($_POST['event_id'])) {
        $event_id = intval($_POST['event_id']);
        $action = $_POST['action'];
        
        try {
            $db = Database::getInstance();
            
            if ($action === 'delete') {
                // Delete contributors first (foreign key constraint)
                $db->query("DELETE FROM contributors WHERE event_id = ?", [$event_id]);
                // Delete the event
                $db->query("DELETE FROM event WHERE id = ?", [$event_id]);
                setFlashMessage('success', 'Event deleted successfully.');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
        }
        
        redirect(smartUrl('admin/events.php'));
    }
}

// Get all event posts
$db = Database::getInstance();
$events = $db->fetchAll(
    "SELECT e.*, u.username as author_username 
     FROM event e 
     LEFT JOIN users u ON e.author_id = u.id 
     ORDER BY e.created_at DESC"
);

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5">Manage Events</h1>
                <div>
                    <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary">Create New Event</a>
                    <a href="<?php echo smartUrl('admin/index.php'); ?>" class="btn btn-secondary">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Events (<?php echo count($events); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($events)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Published</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td><?php echo $event['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($event['content'], 0, 100)); ?>...
                                                </small>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['author_username']); ?></td>
                                            <td><?php echo formatDate($event['publish_date'], 'M d, Y H:i'); ?></td>
                                            <td><?php echo formatDate($event['created_at'], 'M d, Y H:i'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo smartUrl('event/detail.php?id=' . $event['id']); ?>" 
                                                       class="btn btn-outline-info">View</a>
                                                    <a href="<?php echo smartUrl('event/edit.php?id=' . $event['id']); ?>" 
                                                       class="btn btn-outline-warning">Edit</a>
                                                    
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this event?')">
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
                            <h4 class="text-muted">No events yet</h4>
                            <p class="text-muted">Create your first event to get started.</p>
                            <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary">Create First Event</a>
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