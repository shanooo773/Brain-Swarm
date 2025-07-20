<?php
/**
 * Redirect legacy read.php URLs to detail.php
 * This ensures backward compatibility for any bookmarks or external links
 */

// Get the event ID from the URL parameter
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id > 0) {
    // Redirect to the correct detail.php URL with a 301 permanent redirect
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: detail.php?id=' . $event_id);
} else {
    // If no valid ID, redirect to event list
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: list.php');
}

exit();
?>