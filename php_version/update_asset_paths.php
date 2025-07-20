<?php
/**
 * Asset Path Updater Script
 * Updates all asset() calls to smartAsset() and url() calls to smartUrl()
 */

$files_to_update = [
    'index.php',
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

$replacements = [
    "asset('" => "smartAsset('",
    'asset("' => 'smartAsset("',
    "url('" => "smartUrl('",
    'url("' => 'smartUrl("'
];

foreach ($files_to_update as $file) {
    $file_path = __DIR__ . '/' . $file;
    
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        $original_content = $content;
        
        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        
        if ($content !== $original_content) {
            file_put_contents($file_path, $content);
            echo "Updated: $file\n";
        } else {
            echo "No changes needed: $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}

echo "\nAsset path update completed!\n";
?>