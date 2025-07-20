<?php
// Get current user information
$current_user = SessionManager::getUser();
$is_admin = SessionManager::isAdmin();

// Get page title from the including file or use default
$page_title = $page_title ?? 'Brain Swarm';

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;400;700&display=swap" rel="stylesheet">

    <link href="<?php echo asset('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('css/bootstrap-icons.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('css/templatemo-festava-live.css'); ?>" rel="stylesheet">
        
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="<?php echo asset('assets/css/fontawesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('assets/css/templatemo-villa-agency.css'); ?>"> 
    <link rel="stylesheet" href="<?php echo asset('assets/css/owl.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('assets/css/animate.css'); ?>">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>

    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
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

   <div class="sub-header" style="background-color: #f8f9fa; padding: 10px 0;">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-8 col-md-8">
                <ul class="info" style="list-style: none; display: flex; gap: 30px; margin: 0; padding: 0;">
                    <li><i class="fa fa-envelope"></i> info@company.com</li>
                    <li><i class="fa fa-map"></i> Taxila Pakistan</li>
                </ul>
            </div>
            <div class="col">
                <ul class="nav" style="list-style: none; display: flex; gap: 20px; margin-left:0; padding: 0;">
                    <?php if ($current_user): ?>
                        <?php if ($is_admin): ?>
                            <li><a href="<?php echo url('blog/create.php'); ?>"><i class="fa fa-plus"></i> New Blog</a></li>
                        <?php endif; ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa fa-user"></i> <?php echo htmlspecialchars($current_user['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo url('sign-out.php'); ?>">Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo url('sign-in.php'); ?>" style="font-weight: bold;">Sign In</a></li>
                        <li><a href="<?php echo url('sign-up.php'); ?>" style="font-weight: bold;">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="<?php echo url(); ?>" class="logo">
                            <h2>BRAIN</h2><h3>swarm</h3>
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li><a href="<?php echo url(); ?>" <?php echo ($current_page == 'index') ? 'class="active"' : ''; ?>>Home</a></li>
                            <li><a href="<?php echo url('blog/list.php'); ?>" <?php echo (strpos($current_page, 'blog') !== false) ? 'class="active"' : ''; ?>>Blog</a></li>
                            <li><a href="<?php echo url('properties.php'); ?>">Our Team </a></li>
                            <li><a href="<?php echo url('property-details.php'); ?>">Support</a></li>
                            <li><a href="<?php echo url('contact.php'); ?>">Contact Us</a></li>
                            
                            <li><a href="<?php echo url('meeting.php'); ?>"><i class="fa fa-calendar"></i> Schedule a meeting</a></li>
                        </ul>   
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- Messages -->
    <?php 
    $success_message = getFlashMessage('success');
    $error_message = getFlashMessage('error');
    $info_message = getFlashMessage('info');
    $warning_message = getFlashMessage('warning');
    ?>
    
    <?php if ($success_message || $error_message || $info_message || $warning_message): ?>
        <div class="container mt-3">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($info_message): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($info_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($warning_message): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($warning_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Content will be inserted here by including pages -->
    <?php if (isset($content)): ?>
        <?php echo $content; ?>
    <?php endif; ?>

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
                            <a href="#" class="site-footer-link">Home</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Live Stream</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Learn more</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                    <h5 class="site-footer-title mb-3">Have you tried this?</h5>

                    <p class="text-white d-flex mb-1">
                        <a href="#" class="site-footer-link">
                            Dolce Vita Music
                        </a>
                    </p>

                    <p class="text-white d-flex">
                        <a href="#" class="site-footer-link">
                            Membership Login
                        </a>
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0 ms-auto">
                    <h5 class="site-footer-title mb-3">Location</h5>

                    <p class="text-white d-flex mt-3 mb-2">
                        Taxila Pakistan
                    </p>

                    <a href="mailto:info@company.com" class="site-footer-link">
                        info@company.com
                    </a>
                </div>

            </div>
        </div>

        <div class="site-footer-bottom">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-12 mt-5">
                        <p class="copyright-text">Copyright © 2036 Shayan-773</p>
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
                                <a href="#" class="site-footer-link">Your Feedback</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="<?php echo asset('vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo asset('assets/js/isotope.min.js'); ?>"></script>
    <script src="<?php echo asset('assets/js/owl-carousel.js'); ?>"></script>
    <script src="<?php echo asset('assets/js/counter.js'); ?>"></script>
    <script src="<?php echo asset('assets/js/custom.js'); ?>"></script>
    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo asset('js/jquery.sticky.js'); ?>"></script>
    <script src="<?php echo asset('js/click-scroll.js'); ?>"></script>
    <script src="<?php echo asset('js/custom.js'); ?>"></script>

    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
    
    <!-- Custom dropdown styles -->
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