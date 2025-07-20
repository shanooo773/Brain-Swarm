<?php
/**
 * Footer Include with Enhanced JavaScript Loading and CDN Fallbacks
 */

// Define CDN fallbacks for critical JS assets
$cdn_js_assets = [
    'jquery' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
];
?>
    <footer class="site-footer">
        <div class="site-footer-top">
            <div class="container">
                <div class="row">

                    <div class="col-lg-6 col-12">
                        <h2 class="text-white mb-lg-0">Brain Swarm Live</h2>
                    </div>

                    <div class="col-lg-6 col-12 d-flex justify-content-lg-end align-items-center">
                        <ul class="social-icon d-flex justify-content-lg-end">
                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-twitter"></span>
                                </a>
                            </li>

                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-apple"></span>
                                </a>
                            </li>

                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-instagram"></span>
                                </a>
                            </li>

                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-youtube"></span>
                                </a>
                            </li>

                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-pinterest"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">

                <div class="col-lg-6 col-12 mb-4 pb-2">
                    <h5 class="site-footer-title mb-3">Links</h5>

                    <ul class="site-footer-links">
                        <li class="site-footer-link-item">
                            <a href="<?php echo smartUrl(); ?>" class="site-footer-link">Home</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="<?php echo smartUrl('event/list.php'); ?>" class="site-footer-link">Events</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="<?php echo smartUrl('contact.php'); ?>" class="site-footer-link">Contact</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                    <h5 class="site-footer-title mb-3">Have you tried this?</h5>

                    <p class="text-white d-flex mb-1">
                        <a href="#" class="site-footer-link">
                            Brain Swarm Platform
                        </a>
                    </p>

                    <p class="text-white d-flex">
                        <a href="<?php echo smartUrl('sign-in.php'); ?>" class="site-footer-link">
                            Member Login
                        </a>
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0 ms-auto">
                    <h5 class="site-footer-title mb-3">Location</h5>

                    <p class="text-white d-flex mt-3 mb-2">
                        UET Taxila, Pakistan
                    </p>

                    <a href="mailto:info@brainswarm.com" class="site-footer-link">
                        info@brainswarm.com
                    </a>
                </div>

            </div>
        </div>

        <div class="site-footer-bottom">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-12 mt-5">
                        <p class="copyright-text">Copyright © 2025 Brain Swarm</p>
                    </div>

                    <div class="col-lg-8 col-12 mt-lg-5">
                        <ul class="site-footer-links">
                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Terms &amp; Conditions</a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Privacy Policy</a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="<?php echo smartUrl('contact.php'); ?>" class="site-footer-link">Feedback</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript with CDN Fallbacks -->
    <?php
    // jQuery with CDN fallback
    $jquery_url = assetWithFallback('vendor/jquery/jquery.min.js', $cdn_js_assets['jquery']);
    echo "<script src=\"{$jquery_url}\"></script>\n";
    
    // Bootstrap JS with CDN fallback
    $bootstrap_js_url = assetWithFallback('vendor/bootstrap/js/bootstrap.min.js', $cdn_js_assets['bootstrap']);
    echo "<script src=\"{$bootstrap_js_url}\"></script>\n";
    ?>
    
    <!-- Template-specific JavaScript -->
    <script src="<?php echo smartAsset('assets/js/isotope.min.js'); ?>"></script>
    <script src="<?php echo smartAsset('assets/js/owl-carousel.js'); ?>"></script>
    <script src="<?php echo smartAsset('assets/js/counter.js'); ?>"></script>
    <script src="<?php echo smartAsset('assets/js/custom.js'); ?>"></script>
    <script src="<?php echo smartAsset('js/jquery.sticky.js'); ?>"></script>
    <script src="<?php echo smartAsset('js/click-scroll.js'); ?>"></script>
    
    <!-- jQuery dependency check and fallback -->
    <script>
    // Ensure jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.warn('jQuery not loaded, loading from CDN...');
        document.write('<script src="<?php echo $cdn_js_assets['jquery']; ?>"><\/script>');
    }
    
    // Log JS loading status
    document.addEventListener('DOMContentLoaded', function() {
        const scripts = document.querySelectorAll('script[src]');
        scripts.forEach(script => {
            script.onload = () => window.brainSwarmAssets && window.brainSwarmAssets.logAsset(script.src, true);
            script.onerror = () => window.brainSwarmAssets && window.brainSwarmAssets.logAsset(script.src, false);
        });
        
        // Display asset loading summary
        setTimeout(() => {
            if (window.brainSwarmAssets) {
                console.log('=== Brain Swarm Asset Loading Summary ===');
                console.log('Base URL:', window.brainSwarmAssets.baseUrl);
                console.log('Assets Loaded:', window.brainSwarmAssets.assetsLoaded.length);
                console.log('Assets Failed:', window.brainSwarmAssets.assetsFailed.length);
                if (window.brainSwarmAssets.assetsFailed.length > 0) {
                    console.warn('Failed assets:', window.brainSwarmAssets.assetsFailed);
                }
            }
        }, 2000);
    });
    </script>

    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
    
    <!-- Custom dropdown styles and functionality -->
    <style>
    .dropdown {
        position: relative;
    }
    
    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 160px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        border-radius: 8px;
        border: none;
        z-index: 1000;
        margin-top: 5px;
    }
    
    .dropdown-menu.show {
        display: block;
    }
    
    .dropdown-item {
        padding: 8px 16px;
        color: #333;
        text-decoration: none;
        display: block;
        border-radius: 6px;
        margin: 4px;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #ff6b6b;
    }
    </style>
    
    <script>
    // Simple dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        
        if (dropdownToggle && dropdownMenu) {
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });
    </script>
</body>
</html>