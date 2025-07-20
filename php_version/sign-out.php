<?php
require_once 'includes/functions.php';

// Logout user
SessionManager::destroy();
setFlashMessage('success', 'You have been logged out successfully.');
redirect('index.php');
?>