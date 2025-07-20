<?php
/**
 * Header Include with Enhanced Asset Loading and CDN Fallbacks
 */

// Get current user information
$current_user = SessionManager::getUser();
$is_admin = SessionManager::isAdmin();

// Get page title from the including file or use default
$page_title = $page_title ?? 'Brain Swarm';

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define CDN fallbacks for critical assets
$cdn_assets = [
    'bootstrap_css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'bootstrap_js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'jquery' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'bootstrap_icons' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Preconnect to external domains for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;400;700&display=swap" rel="stylesheet">

    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Critical CSS with fallbacks -->
    <?php
    // Bootstrap CSS with CDN fallback
    $bootstrap_css = assetWithFallback('vendor/bootstrap/css/bootstrap.min.css', $cdn_assets['bootstrap_css']);
    echo "<link href=\"{$bootstrap_css}\" rel=\"stylesheet\">\n";
    
    // Bootstrap Icons with CDN fallback  
    $bootstrap_icons = assetWithFallback('css/bootstrap-icons.css', $cdn_assets['bootstrap_icons']);
    echo "<link href=\"{$bootstrap_icons}\" rel=\"stylesheet\">\n";
    
    // Font Awesome with CDN fallback
    $fontawesome = assetWithFallback('assets/css/fontawesome.css', $cdn_assets['fontawesome']);
    echo "<link href=\"{$fontawesome}\" rel=\"stylesheet\">\n";
    ?>
    
    <!-- Template-specific CSS -->
    <link href="<?php echo smartAsset('css/templatemo-festava-live.css'); ?>" rel="stylesheet">
    <link href="<?php echo smartAsset('assets/css/templatemo-villa-agency.css'); ?>" rel="stylesheet"> 
    <link href="<?php echo smartAsset('assets/css/owl.css'); ?>" rel="stylesheet">
    <link href="<?php echo smartAsset('assets/css/animate.css'); ?>" rel="stylesheet">
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>

    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
    
    <!-- Asset Loading Status for Debugging -->
    <script>
    // Log asset loading status for debugging
    window.brainSwarmAssets = {
        baseUrl: '<?php echo getBaseUrl(); ?>',
        assetsLoaded: [],
        assetsFailed: [],
        logAsset: function(url, success) {
            if (success) {
                this.assetsLoaded.push(url);
                console.log('✓ Loaded:', url);
            } else {
                this.assetsFailed.push(url);
                console.error('✗ Failed:', url);
            }
        }
    };
    
    // Monitor CSS loading
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('link[rel="stylesheet"]');
        links.forEach(link => {
            link.onload = () => window.brainSwarmAssets.logAsset(link.href, true);
            link.onerror = () => window.brainSwarmAssets.logAsset(link.href, false);
        });
    });
    </script>
</head>

<body>
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->