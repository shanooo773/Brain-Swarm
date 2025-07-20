<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$blog_id) {
    setFlashMessage('error', 'Blog post not found.');
    redirect(smartUrl('blog/list.php'));
}

// Get blog post
$db = Database::getInstance();
$blog = $db->fetch("SELECT * FROM blogs WHERE id = ?", [$blog_id]);

if (!$blog) {
    setFlashMessage('error', 'Blog post not found.');
    redirect(smartUrl('blog/list.php'));
}

$page_title = 'Delete: ' . htmlspecialchars($blog['title']);

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        try {
            // Delete associated contributors first
            $db->query("DELETE FROM contributors WHERE blog_id = ?", [$blog_id]);
            
            // Delete the blog post
            $db->query("DELETE FROM blogs WHERE id = ?", [$blog_id]);
            
            // Delete associated image file if it exists
            if (!empty($blog['image']) && file_exists(BLOG_IMAGES_DIR . $blog['image'])) {
                unlink(BLOG_IMAGES_DIR . $blog['image']);
            }
            
            setFlashMessage('success', 'Blog post deleted successfully!');
            redirect(smartUrl('blog/list.php'));
        } catch (Exception $e) {
            setFlashMessage('error', 'There was an error deleting the blog post. Please try again.');
        }
    } else {
        // User cancelled, redirect back
        redirect(smartUrl('blog/detail.php?id=' . $blog_id));
    }
}

// Start output buffering for content
ob_start();
?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Deletion
                    </h4>
                </div>
                <div class="card-body">
                    <p class="mb-3">Are you sure you want to delete this blog post? This action cannot be undone.</p>
                    
                    <div class="blog-preview p-3 bg-light rounded mb-4">
                        <h5 class="mb-2"><?php echo htmlspecialchars($blog['title']); ?></h5>
                        
                        <?php if (!empty($blog['image'])): ?>
                            <img src="<?php echo smartUrl('uploads/blog_images/' . $blog['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                 class="img-fluid rounded mb-2" 
                                 style="max-height: 150px;">
                        <?php endif; ?>
                        
                        <p class="text-muted mb-1">
                            Published: <?php echo formatDate($blog['publish_date'], 'F d, Y'); ?>
                        </p>
                        
                        <p class="mb-0">
                            <?php echo htmlspecialchars(substr($blog['content'], 0, 150)) . (strlen($blog['content']) > 150 ? '...' : ''); ?>
                        </p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i> What will be deleted:
                        </h6>
                        <ul class="mb-0">
                            <li>The blog post and all its content</li>
                            <li>Any associated contributors</li>
                            <?php if (!empty($blog['image'])): ?>
                                <li>The uploaded featured image</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <form method="post">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?php echo smartUrl('blog/detail.php?id=' . $blog_id); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                                <a href="<?php echo smartUrl('blog/list.php'); ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-list"></i> All Posts
                                </a>
                            </div>
                            <button type="submit" name="confirm_delete" value="1" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Yes, Delete Post
                            </button>
                        </div>
                    </form>
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
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.blog-preview {
    border: 1px solid #dee2e6;
}

.blog-preview img {
    object-fit: cover;
    width: 100%;
}

.btn {
    border-radius: 8px;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.alert {
    border-radius: 8px;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>