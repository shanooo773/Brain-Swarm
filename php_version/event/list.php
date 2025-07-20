<?php
require_once '../includes/functions.php';

$page_title = 'Events - Brain Swarm';

// Get all event posts
$db = Database::getInstance();
$events = $db->fetchAll(
    "SELECT e.*, u.username as author_username 
     FROM event e 
     LEFT JOIN users u ON e.author_id = u.id 
     ORDER BY e.publish_date DESC"
);

// Function to truncate words
function truncateWords($text, $limit = 30) {
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
    return $text;
}

// Start output buffering for content
ob_start();
?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-4">Events</h1>
                <?php if (SessionManager::isAdmin()): ?>
                    <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Event
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?php echo smartUrl('uploads/event_images/' . $event['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                 class="card-img-top expandable-image" 
                                 data-full-src="<?php echo smartUrl('uploads/event_images/' . $event['image']); ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text flex-grow-1">
                                <?php echo htmlspecialchars(truncateWords($event['content'])); ?>
                            </p>
                            <div class="mt-auto">
                                <small class="text-muted">
                                    By <?php echo htmlspecialchars($event['author_username']); ?> • 
                                    <?php echo formatDate($event['publish_date'], 'M d, Y'); ?>
                                </small>
                                <div class="mt-2">
                                    <a href="<?php echo smartUrl('event/detail.php?id=' . $event['id']); ?>" 
                                       class="btn btn-outline-primary btn-sm">View</a>
                                    
                                    <?php if (SessionManager::isAdmin()): ?>
                                        <div class="btn-group btn-group-sm mt-2" role="group">
                                            <a href="<?php echo smartUrl('event/edit.php?id=' . $event['id']); ?>" 
                                               class="btn btn-outline-warning">Edit</a>
                                            <a href="<?php echo smartUrl('event/delete.php?id=' . $event['id']); ?>" 
                                               class="btn btn-outline-danger">Delete</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <h3 class="text-muted">No events yet</h3>
                    <p class="text-muted">Check back later for new content!</p>
                    <?php if (SessionManager::isAdmin()): ?>
                        <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary mt-3">Create First Event</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Add custom styles and scripts
$extra_css = '
<style>
.card {
    border: none;
    border-radius: 12px;
    transition: transform 0.2s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.expandable-image {
    position: relative;
}

/* Full size image overlay */
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

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    border-radius: 8px;
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
            e.preventDefault(); // Prevent card click navigation
            e.stopPropagation(); // Stop event bubbling
            
            const fullSrc = this.getAttribute("data-full-src") || this.src;
            overlayImg.src = fullSrc;
            overlayImg.alt = this.alt;
            overlay.classList.add("active");
            document.body.style.overflow = "hidden"; // Prevent background scrolling
        });
    });

    // Close overlay when clicking close button
    closeBtn.addEventListener("click", function() {
        overlay.classList.remove("active");
        document.body.style.overflow = "auto"; // Restore scrolling
    });

    // Close overlay when clicking outside the image
    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) {
            overlay.classList.remove("active");
            document.body.style.overflow = "auto"; // Restore scrolling
        }
    });

    // Close overlay with ESC key
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && overlay.classList.contains("active")) {
            overlay.classList.remove("active");
            document.body.style.overflow = "auto"; // Restore scrolling
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