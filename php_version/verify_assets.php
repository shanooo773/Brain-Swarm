<?php
/**
 * Asset Verification Script for Brain Swarm PHP Website
 * This script checks if all CSS, JS, and image assets exist and logs missing ones
 */

require_once 'includes/functions.php';

// Define all assets that should exist
$assets_to_check = [
    // CSS Files
    'css' => [
        'vendor/bootstrap/css/bootstrap.min.css',
        'css/bootstrap.min.css', 
        'css/bootstrap-icons.css',
        'css/templatemo-festava-live.css',
        'assets/css/fontawesome.css',
        'assets/css/templatemo-villa-agency.css',
        'assets/css/owl.css',
        'assets/css/animate.css'
    ],
    
    // JavaScript Files  
    'js' => [
        'vendor/jquery/jquery.min.js',
        'vendor/bootstrap/js/bootstrap.min.js',
        'assets/js/isotope.min.js',
        'assets/js/owl-carousel.js', 
        'assets/js/counter.js',
        'assets/js/custom.js',
        'js/jquery.min.js',
        'js/bootstrap.min.js',
        'js/jquery.sticky.js',
        'js/click-scroll.js',
        'js/custom.js'
    ],
    
    // Image Files (commonly referenced)
    'images' => [
        'assets/images/featured.jpg',
        'assets/images/featured-icon.png',
        'assets/images/info-icon-01.png',
        'assets/images/info-icon-02.png', 
        'assets/images/info-icon-03.png',
        'assets/images/info-icon-04.png',
        'assets/images/video-frame.jpg',
        'assets/images/single1-property.jpg',
        'assets/images/single2.jpg',
        'assets/images/single3.jpg',
        'assets/images/phone-icon.png',
        'assets/images/email-icon.png',
        'images/artists/joecalih-UmTZqmMvQcw-unsplash.jpg',
        'images/artists/abstral-official-bdlMO9z5yco-unsplash.jpg', 
        'images/artists/soundtrap-rAT6FJ6wltE-unsplash.jpg'
    ],
    
    // Video Files
    'video' => [
        'video/pexels-2022395.mp4'
    ]
];

$base_path = __DIR__ . '/static/';
$missing_assets = [];
$existing_assets = [];

echo "<h1>Brain Swarm Asset Verification Report</h1>\n";
echo "<p>Base path: " . htmlspecialchars($base_path) . "</p>\n";
echo "<p>Site URL: " . SITE_URL . "</p>\n";

foreach ($assets_to_check as $type => $assets) {
    echo "<h2>" . ucfirst($type) . " Files</h2>\n";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr><th style='padding: 8px;'>Asset Path</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Full URL</th></tr>\n";
    
    foreach ($assets as $asset) {
        $full_path = $base_path . $asset;
        $url = asset($asset);
        $exists = file_exists($full_path);
        
        if ($exists) {
            $existing_assets[] = $asset;
            $status = "<span style='color: green;'>✓ EXISTS</span>";
            $file_size = filesize($full_path);
            $status .= " (" . number_format($file_size / 1024, 1) . " KB)";
        } else {
            $missing_assets[] = $asset;
            $status = "<span style='color: red;'>✗ MISSING</span>";
        }
        
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($asset) . "</td>";
        echo "<td style='padding: 8px;'>" . $status . "</td>";
        echo "<td style='padding: 8px;'><a href='" . htmlspecialchars($url) . "' target='_blank'>" . htmlspecialchars($url) . "</a></td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
}

// Summary
echo "<h2>Summary</h2>\n";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
echo "<p><strong>Total Assets Checked:</strong> " . array_sum(array_map('count', $assets_to_check)) . "</p>\n";
echo "<p><strong>Existing Assets:</strong> <span style='color: green;'>" . count($existing_assets) . "</span></p>\n";
echo "<p><strong>Missing Assets:</strong> <span style='color: red;'>" . count($missing_assets) . "</span></p>\n";
echo "</div>\n";

if (!empty($missing_assets)) {
    echo "<h3>Missing Assets Details</h3>\n";
    echo "<div style='background: #ffe6e6; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
    echo "<ul>\n";
    foreach ($missing_assets as $asset) {
        echo "<li style='color: red;'>" . htmlspecialchars($asset) . "</li>\n";
    }
    echo "</ul>\n";
    echo "</div>\n";
}

// Browser DevTools debugging guide
echo "<h2>Browser DevTools Debugging Guide</h2>\n";
echo "<div style='background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
echo "<h3>How to Check Asset Loading in Browser:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Open DevTools:</strong> Press F12 or right-click → Inspect Element</li>\n";
echo "<li><strong>Go to Console Tab:</strong> Look for 404 errors (red text)</li>\n";
echo "<li><strong>Go to Network Tab:</strong> Refresh page and see failed requests (red status codes)</li>\n";
echo "<li><strong>Check Sources Tab:</strong> Verify if files are loaded correctly</li>\n";
echo "</ol>\n";
echo "<h3>Common Issues:</h3>\n";
echo "<ul>\n";
echo "<li><strong>404 Not Found:</strong> File doesn't exist at the specified path</li>\n";
echo "<li><strong>MIME Type Errors:</strong> Server not configured for CSS/JS files</li>\n";
echo "<li><strong>Cache Issues:</strong> Try Ctrl+F5 to hard refresh</li>\n";
echo "<li><strong>Relative Path Issues:</strong> Check if paths work from different subdirectories</li>\n";
echo "</ul>\n";
echo "</div>\n";

// CDN recommendations
echo "<h2>Recommended CDN Fallbacks</h2>\n";
echo "<div style='background: #e6f3ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
echo "<h3>Bootstrap CSS:</h3>\n";
echo "<code>&lt;link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\"&gt;</code>\n";
echo "<h3>Bootstrap JS:</h3>\n";
echo "<code>&lt;script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"&gt;&lt;/script&gt;</code>\n";
echo "<h3>jQuery:</h3>\n";
echo "<code>&lt;script src=\"https://code.jquery.com/jquery-3.7.1.min.js\"&gt;&lt;/script&gt;</code>\n";
echo "<h3>Font Awesome:</h3>\n";
echo "<code>&lt;link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\"&gt;</code>\n";
echo "</div>\n";

// Log results to file as well
$log_content = "Brain Swarm Asset Verification - " . date('Y-m-d H:i:s') . "\n";
$log_content .= "========================================\n";
$log_content .= "Total assets checked: " . array_sum(array_map('count', $assets_to_check)) . "\n";
$log_content .= "Existing assets: " . count($existing_assets) . "\n";
$log_content .= "Missing assets: " . count($missing_assets) . "\n\n";

if (!empty($missing_assets)) {
    $log_content .= "MISSING ASSETS:\n";
    foreach ($missing_assets as $asset) {
        $log_content .= "- " . $asset . "\n";
    }
}

$log_content .= "\nEXISTING ASSETS:\n";
foreach ($existing_assets as $asset) {
    $log_content .= "- " . $asset . "\n";
}

file_put_contents(__DIR__ . '/asset_verification.log', $log_content);

echo "<p><em>Results also saved to asset_verification.log</em></p>\n";
?>