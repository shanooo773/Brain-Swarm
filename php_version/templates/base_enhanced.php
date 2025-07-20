<?php
/**
 * Enhanced Base Template for Brain Swarm Website
 * This template uses modular includes and smart asset loading
 */

// Include the header (DOCTYPE, head section, CSS loading)
include __DIR__ . '/../includes/header.php';

// Include the navigation (top bar and main menu)
include __DIR__ . '/../includes/navigation.php';

// Content will be inserted here by including pages
if (isset($content)) {
    echo $content;
}

// Include the footer (footer content, JavaScript loading)
include __DIR__ . '/../includes/footer.php';
?>