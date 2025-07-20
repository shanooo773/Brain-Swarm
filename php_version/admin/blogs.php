<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Manage Blogs - Brain Swarm Admin';

// Handle blog actions
if ($_POST) {
    if (isset($_POST['action']) && isset($_POST['blog_id'])) {
        $blog_id = intval($_POST['blog_id']);
        $action = $_POST['action'];
        
        try {
            $db = Database::getInstance();
            
            if ($action === 'delete') {
                // Delete contributors first (foreign key constraint)
                $db->query("DELETE FROM contributors WHERE blog_id = ?", [$blog_id]);
                // Delete the blog
                $db->query("DELETE FROM blogs WHERE id = ?", [$blog_id]);
                setFlashMessage('success', 'Blog post deleted successfully.');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
        }
        
        redirect(smartUrl('admin/blogs.php'));
    }
}

// Get all blog posts
$db = Database::getInstance();
$blogs = $db->fetchAll(
    "SELECT b.*, u.username as author_username 
     FROM blogs b 
     LEFT JOIN users u ON b.author_id = u.id 
     ORDER BY b.created_at DESC"
);

// Start output buffering for content
ob_start();
?>

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5">Manage Blog Posts</h1>
                <div>
                    <a href="<?php echo smartUrl('blog/create.php'); ?>" class="btn btn-primary">Create New Blog</a>
                    <a href="<?php echo smartUrl('admin/index.php'); ?>" class="btn btn-secondary">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Blog Posts (<?php echo count($blogs); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($blogs)): ?>
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
                                    <?php foreach ($blogs as $blog): ?>
                                        <tr>
                                            <td><?php echo $blog['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($blog['title']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($blog['content'], 0, 100)); ?>...
                                                </small>
                                            </td>
                                            <td><?php echo htmlspecialchars($blog['author_username']); ?></td>
                                            <td><?php echo formatDate($blog['publish_date'], 'M d, Y H:i'); ?></td>
                                            <td><?php echo formatDate($blog['created_at'], 'M d, Y H:i'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo smartUrl('blog/detail.php?id=' . $blog['id']); ?>" 
                                                       class="btn btn-outline-info">View</a>
                                                    <a href="<?php echo smartUrl('blog/edit.php?id=' . $blog['id']); ?>" 
                                                       class="btn btn-outline-warning">Edit</a>
                                                    
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this blog post?')">
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
                            <h4 class="text-muted">No blog posts yet</h4>
                            <p class="text-muted">Create your first blog post to get started.</p>
                            <a href="<?php echo smartUrl('blog/create.php'); ?>" class="btn btn-primary">Create First Blog Post</a>
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