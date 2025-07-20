<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$blog_id) {
    setFlashMessage('error', 'Blog post not found.');
    redirect(url('blog/list.php'));
}

// Get existing blog post
$db = Database::getInstance();
$blog = $db->fetch("SELECT * FROM blogs WHERE id = ?", [$blog_id]);

if (!$blog) {
    setFlashMessage('error', 'Blog post not found.');
    redirect(url('blog/list.php'));
}

$page_title = 'Edit: ' . htmlspecialchars($blog['title']);

$errors = [];
$form_data = $blog; // Initialize form with existing data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    
    // Store form data to repopulate on error
    $form_data = array_merge($form_data, compact('title', 'content'));
    
    // Validate input
    if ($error = validateRequired($title, 'Title')) $errors[] = $error;
    if ($error = validateRequired($content, 'Content')) $errors[] = $error;
    if ($error = validateLength($title, 3, 200, 'Title')) $errors[] = $error;
    
    // Handle image upload
    $image_filename = $blog['image']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFile($_FILES['image'], BLOG_IMAGES_DIR, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        if ($upload_result['success']) {
            // Delete old image if it exists
            if (!empty($blog['image']) && file_exists(BLOG_IMAGES_DIR . $blog['image'])) {
                unlink(BLOG_IMAGES_DIR . $blog['image']);
            }
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        try {
            $db->query(
                "UPDATE blogs SET title = ?, content = ?, image = ?, updated_at = NOW() WHERE id = ?",
                [$title, $content, $image_filename, $blog_id]
            );
            
            setFlashMessage('success', 'Blog post updated successfully!');
            redirect(url('blog/detail.php?id=' . $blog_id));
        } catch (Exception $e) {
            $errors[] = 'There was an error updating the blog post. Please try again.';
        }
    }
}

// Start output buffering for content
ob_start();
?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Edit Blog Post</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Blog Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="Enter blog title" 
                                   value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image</label>
                            
                            <?php if (!empty($blog['image'])): ?>
                                <div class="current-image mb-2">
                                    <p class="text-muted">Current image:</p>
                                    <img src="<?php echo url('uploads/blog_images/' . $blog['image']); ?>" 
                                         alt="Current blog image" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 150px;">
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">
                                <?php if (!empty($blog['image'])): ?>
                                    Upload a new image to replace the current one, or leave empty to keep the current image.
                                <?php else: ?>
                                    Upload an image (optional).
                                <?php endif ?>
                                Supported formats: JPG, PNG, GIF, WebP. Max size: 10MB
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Blog Content</label>
                            <textarea class="form-control" id="content" name="content" rows="15" 
                                      placeholder="Write your blog content here..." required><?php echo htmlspecialchars($form_data['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?php echo url('blog/detail.php?id=' . $blog_id); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Post
                                </a>
                                <a href="<?php echo url('blog/list.php'); ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-list"></i> All Posts
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Blog Post
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
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 12px 12px 0 0 !important;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
}

.form-control:focus {
    border-color: #ff6b6b;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    border-radius: 8px;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
}

.btn-secondary, .btn-outline-secondary {
    border-radius: 8px;
}

textarea {
    resize: vertical;
    min-height: 300px;
}

.current-image {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.img-thumbnail {
    border-radius: 8px;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>