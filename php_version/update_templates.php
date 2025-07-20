<?php
/**
 * Template Updater Script
 * Updates all PHP files to use the enhanced base template
 */

$files_to_update = [
    'contact.php',
    'properties.php', 
    'property-details.php',
    'meeting.php',
    'sign-in.php',
    'sign-up.php',
    'blog/list.php',
    'blog/detail.php',
    'blog/create.php',
    'blog/edit.php'
];

foreach ($files_to_update as $file) {
    $file_path = __DIR__ . '/' . $file;
    
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        $updated = false;
        
        // Update base template include
        if (strpos($content, "include 'templates/base.php';") !== false) {
            $content = str_replace("include 'templates/base.php';", "include 'templates/base_enhanced.php';", $content);
            $updated = true;
        }
        
        if ($updated) {
            file_put_contents($file_path, $content);
            echo "Updated template in: $file\n";
        } else {
            echo "No template update needed: $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}

echo "\nTemplate update completed!\n";
?>