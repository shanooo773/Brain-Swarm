<?php
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Create Event - Brain Swarm';

$errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    
    // Store form data to repopulate on error
    $form_data = compact('title', 'content');
    
    // Validate input
    if ($error = validateRequired($title, 'Title')) $errors[] = $error;
    if ($error = validateRequired($content, 'Content')) $errors[] = $error;
    if ($error = validateLength($title, 3, 200, 'Title')) $errors[] = $error;
    
    // Handle image upload
    $image_filename = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFile($_FILES['image'], EVENT_IMAGES_DIR, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        if ($upload_result['success']) {
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            $current_user = SessionManager::getUser();
            
            $db->query(
                "INSERT INTO event (title, content, author_id, image, publish_date) VALUES (?, ?, ?, ?, NOW())",
                [$title, $content, $current_user['id'], $image_filename]
            );
            
            $event_id = $db->lastInsertId();
            
            setFlashMessage('success', 'Event created successfully!');
            redirect(smartUrl('event/detail.php?id=' . $event_id));
        } catch (Exception $e) {
            $errors[] = 'There was an error creating the event. Please try again.';
            
            // Clean up uploaded image if database insert failed
            if ($image_filename && file_exists(EVENT_IMAGES_DIR . $image_filename)) {
                unlink(EVENT_IMAGES_DIR . $image_filename);
            }
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
                    <h2 class="mb-0">Create New Event</h2>
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
                            <label for="title" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="Enter event title" 
                                   value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image (Optional)</label>
                            <input type="file" class="form-control" id="image" name="image" 
                                   accept="image/*">
                            <div class="form-text">Supported formats: JPG, PNG, GIF, WebP. Max size: 10MB</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Event Content</label>
                            <textarea class="form-control" id="content" name="content" rows="15" 
                                      placeholder="Write your event content here..." required><?php echo htmlspecialchars($form_data['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo smartUrl('event/list.php'); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Events
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Event
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

.btn-secondary {
    border-radius: 8px;
}

textarea {
    resize: vertical;
    min-height: 300px;
}
</style>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>