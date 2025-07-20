<?php
require_once '../includes/functions.php';

// Get event ID from URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    setFlashMessage('error', 'Event not found.');
    redirect(smartUrl('event/list.php'));
}

// Get event post with author info
$db = Database::getInstance();
$event = $db->fetch(
    "SELECT e.*, u.username as author_username 
     FROM event e 
     LEFT JOIN users u ON e.author_id = u.id 
     WHERE e.id = ?",
    [$event_id]
);

if (!$event) {
    setFlashMessage('error', 'Event not found.');
    redirect(smartUrl('event/list.php'));
}

$page_title = htmlspecialchars($event['title']) . ' - Brain Swarm';

// Get contributors for this event
$contributors = $db->fetchAll(
    "SELECT * FROM contributors WHERE event_id = ? ORDER BY name",
    [$event_id]
);

// Start output buffering for content
ob_start();
?>

<div class="container mt-5 pt-5">    
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-<?php echo !empty($contributors) ? '8' : '12'; ?>">
            <article class="event-post">
                <!-- Featured Image -->
                <?php if (!empty($event['image'])): ?>
                    <div class="featured-image mb-4">
                        <img src="<?php echo smartUrl('uploads/event_images/' . $event['image']); ?>" 
                             alt="<?php echo htmlspecialchars($event['title']); ?>" 
                             class="img-fluid rounded expandable-image event-image" 
                             data-full-src="<?php echo smartUrl('uploads/event_images/' . $event['image']); ?>">
                    </div>
                <?php endif; ?>
                
                <!-- Event Header -->
                <header class="mb-4">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="event-meta">
                            <span class="text-muted">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($event['author_username']); ?>
                            </span>
                            <span class="text-muted ms-3">
                                <i class="bi bi-calendar"></i> <?php echo formatDate($event['publish_date'], 'F d, Y'); ?>
                            </span>
                        </div>
                        
                        <?php if (SessionManager::isAdmin()): ?>
                            <div class="btn-group" role="group">
                                <a href="<?php echo smartUrl('event/edit.php?id=' . $event['id']); ?>" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="<?php echo smartUrl('event/delete.php?id=' . $event['id']); ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
                
                <!-- Event Content -->
                <div class="event-content">
                    <?php echo nl2br(htmlspecialchars($event['content'])); ?>
                </div>
                
                <!-- Navigation -->
                <div class="event-navigation mt-5 pt-4 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo smartUrl('event/list.php'); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Back to Events
                            </a>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if (SessionManager::isAdmin()): ?>
                                <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> New Event
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
        </div>
        
        <!-- Sidebar (if contributors exist) -->
        <?php if (!empty($contributors)): ?>
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Contributors</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($contributors as $contributor): ?>
                                <div class="contributor mb-3 pb-3 <?php echo $contributor !== end($contributors) ? 'border-bottom' : ''; ?>">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($contributor['name']); ?></h6>
                                    
                                    <?php if (!empty($contributor['email'])): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-envelope text-muted"></i>
                                            <a href="mailto:<?php echo htmlspecialchars($contributor['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($contributor['email']); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="social-links">
                                        <?php if (!empty($contributor['github'])): ?>
                                            <a href="<?php echo htmlspecialchars($contributor['github']); ?>" target="_blank" class="text-decoration-none me-2">
                                                <i class="bi bi-github"></i> GitHub
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($contributor['linkedin'])): ?>
                                            <a href="<?php echo htmlspecialchars($contributor['linkedin']); ?>" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-linkedin"></i> LinkedIn
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Add custom styles
$extra_css = '
<style>
.event-post {
    font-size: 1.1rem;
    line-height: 1.8;
}

.event-content {
    margin-bottom: 2rem;
}

.event-meta {
    font-size: 0.95rem;
}

.featured-image {
    overflow: hidden;
    border-radius: 12px;
}

.featured-image img {
    width: 100%;
    height: auto;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.featured-image img:hover {
    transform: scale(1.1);
}

.event-image {
    transition: transform 0.3s ease;
}

.event-image:hover {
    transform: scale(1.1);
}

.expandable-image {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.sidebar .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.contributor {
    font-size: 0.9rem;
}

.social-links a {
    font-size: 0.85rem;
    color: #6c757d;
}

.social-links a:hover {
    color: #ff6b6b;
}

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
}

.btn-outline-primary {
    border-color: #ff6b6b;
    color: #ff6b6b;
}

.btn-outline-primary:hover {
    background-color: #ff6b6b;
    border-color: #ff6b6b;
}

/* Image overlay for expandable images */
.image-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.image-overlay.active {
    opacity: 1;
    visibility: visible;
}

.image-overlay img {
    max-width: 95vw;
    max-height: 95vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.image-overlay .close-btn {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 30px;
    cursor: pointer;
    z-index: 10000;
    transition: transform 0.2s ease;
}

.image-overlay .close-btn:hover {
    transform: scale(1.2);
}
</style>
';

$extra_js = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Create overlay for full-size image display
    const overlay = document.createElement("div");
    overlay.className = "image-overlay";
    overlay.innerHTML = "<span class=\"close-btn\">&times;</span><img src=\"\" alt=\"\">";
    document.body.appendChild(overlay);

    const overlayImg = overlay.querySelector("img");
    const closeBtn = overlay.querySelector(".close-btn");

    // Handle expandable image clicks
    document.querySelectorAll(".expandable-image").forEach(img => {
        img.addEventListener("click", function(e) {
            e.preventDefault();
            
            const fullSrc = this.getAttribute("data-full-src") || this.src;
            overlayImg.src = fullSrc;
            overlayImg.alt = this.alt;
            overlay.classList.add("active");
            document.body.style.overflow = "hidden";
        });
    });

    // Close overlay
    closeBtn.addEventListener("click", function() {
        overlay.classList.remove("active");
        document.body.style.overflow = "auto";
    });

    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) {
            overlay.classList.remove("active");
            document.body.style.overflow = "auto";
        }
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && overlay.classList.contains("active")) {
            overlay.classList.remove("active");
            document.body.style.overflow = "auto";
        }
    });
});
</script>
';

// Get the content
$content = ob_get_clean();

// Include the base template
include '../templates/base.php';
?>