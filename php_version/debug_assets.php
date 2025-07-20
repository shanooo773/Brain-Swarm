<?php
/**
 * Asset Debugging Tool
 * Use this page to test asset loading and debug path issues
 */

require_once 'includes/functions.php';

$page_title = 'Asset Debug Tool - Brain Swarm';

// Start output buffering for content
ob_start();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1>🔧 Asset Debugging Tool</h1>
            <p class="lead">Use this tool to test asset loading and debug path issues in your PHP website.</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>📊 Environment Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Current URL:</strong></td>
                            <td><code><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>HTTP Host:</strong></td>
                            <td><code><?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Script Name:</strong></td>
                            <td><code><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Document Root:</strong></td>
                            <td><code><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Base URL (Smart):</strong></td>
                            <td><code><?php echo htmlspecialchars(getBaseUrl()); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Site URL (Config):</strong></td>
                            <td><code><?php echo htmlspecialchars(SITE_URL); ?></code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>🔗 URL Generation Tests</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>smartUrl():</strong></td>
                            <td><code><?php echo htmlspecialchars(smartUrl()); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>smartUrl('contact.php'):</strong></td>
                            <td><code><?php echo htmlspecialchars(smartUrl('contact.php')); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>smartAsset('css/style.css'):</strong></td>
                            <td><code><?php echo htmlspecialchars(smartAsset('css/style.css')); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>smartAsset('js/main.js'):</strong></td>
                            <td><code><?php echo htmlspecialchars(smartAsset('js/main.js')); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Legacy url():</strong></td>
                            <td><code><?php echo htmlspecialchars(url()); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Legacy asset():</strong></td>
                            <td><code><?php echo htmlspecialchars(asset('css/style.css')); ?></code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>🧪 Live Asset Tests</h3>
                </div>
                <div class="card-body">
                    <p>These tests will actually try to load assets and show if they work:</p>
                    
                    <h4>CSS Test</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Bootstrap CSS:</strong></p>
                            <link rel="stylesheet" href="<?php echo smartAsset('vendor/bootstrap/css/bootstrap.min.css'); ?>" id="test-bootstrap">
                            <code><?php echo smartAsset('vendor/bootstrap/css/bootstrap.min.css'); ?></code>
                            <div id="bootstrap-status" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Custom CSS:</strong></p>
                            <link rel="stylesheet" href="<?php echo smartAsset('css/templatemo-festava-live.css'); ?>" id="test-custom">
                            <code><?php echo smartAsset('css/templatemo-festava-live.css'); ?></code>
                            <div id="custom-status" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>FontAwesome:</strong></p>
                            <link rel="stylesheet" href="<?php echo smartAsset('assets/css/fontawesome.css'); ?>" id="test-fontawesome">
                            <code><?php echo smartAsset('assets/css/fontawesome.css'); ?></code>
                            <div id="fontawesome-status" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4">JavaScript Test</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>jQuery:</strong></p>
                            <code><?php echo smartAsset('vendor/jquery/jquery.min.js'); ?></code>
                            <div id="jquery-status" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Bootstrap JS:</strong></p>
                            <code><?php echo smartAsset('vendor/bootstrap/js/bootstrap.min.js'); ?></code>
                            <div id="bootstrap-js-status" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Custom JS:</strong></p>
                            <code><?php echo smartAsset('assets/js/custom.js'); ?></code>
                            <div id="custom-js-status" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4">Image Test</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Featured Image:</strong></p>
                            <img src="<?php echo smartAsset('assets/images/featured.jpg'); ?>" 
                                 alt="Test Image" 
                                 style="max-width: 200px; height: auto;"
                                 onload="document.getElementById('image-status').innerHTML='<span class=\'text-success\'>✅ Image loaded successfully</span>'"
                                 onerror="document.getElementById('image-status').innerHTML='<span class=\'text-danger\'>❌ Image failed to load</span>'">
                            <div id="image-status" class="mt-2"></div>
                            <small><code><?php echo smartAsset('assets/images/featured.jpg'); ?></code></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>📝 Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="verify_assets.php" class="btn btn-primary">🔍 Run Asset Verification</a>
                        <a href="<?php echo smartUrl(); ?>" class="btn btn-success">🏠 Back to Home</a>
                        <button onclick="location.reload();" class="btn btn-secondary">🔄 Refresh Page</button>
                        <button onclick="console.log(window.brainSwarmAssets);" class="btn btn-info">📊 Check Console</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Test asset loading status
document.addEventListener('DOMContentLoaded', function() {
    // Test CSS loading
    function testStylesheet(id, statusId) {
        const link = document.getElementById(id);
        const status = document.getElementById(statusId);
        
        if (link) {
            link.onload = () => status.innerHTML = '<span class="text-success">✅ Loaded</span>';
            link.onerror = () => status.innerHTML = '<span class="text-danger">❌ Failed</span>';
        }
    }
    
    testStylesheet('test-bootstrap', 'bootstrap-status');
    testStylesheet('test-custom', 'custom-status');
    testStylesheet('test-fontawesome', 'fontawesome-status');
    
    // Test JavaScript loading
    function testScript(url, statusId) {
        const script = document.createElement('script');
        const status = document.getElementById(statusId);
        
        script.onload = () => status.innerHTML = '<span class="text-success">✅ Loaded</span>';
        script.onerror = () => status.innerHTML = '<span class="text-danger">❌ Failed</span>';
        script.src = url;
        
        document.head.appendChild(script);
    }
    
    testScript('<?php echo smartAsset('vendor/jquery/jquery.min.js'); ?>', 'jquery-status');
    testScript('<?php echo smartAsset('vendor/bootstrap/js/bootstrap.min.js'); ?>', 'bootstrap-js-status');
    testScript('<?php echo smartAsset('assets/js/custom.js'); ?>', 'custom-js-status');
    
    // Log debug info to console
    console.log('=== Brain Swarm Debug Tool ===');
    console.log('Base URL:', '<?php echo getBaseUrl(); ?>');
    console.log('Site URL:', '<?php echo SITE_URL; ?>');
    console.log('Current Page:', window.location.href);
});
</script>

<?php
// Get the content
$content = ob_get_clean();

// Include the enhanced base template
include 'templates/base_enhanced.php';
?>