<?php
/**
 * Navigation Include for Brain Swarm Website
 */

// Get current user and page info
$current_user = SessionManager::getUser();
$is_admin = SessionManager::isAdmin();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="sub-header" style="background-color: #f8f9fa; padding: 10px 0;">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-8 col-md-8">
                <ul class="info" style="list-style: none; display: flex; gap: 30px; margin: 0; padding: 0;">
                    <li><i class="fa fa-envelope"></i> info@brainswarm.com</li>
                    <li><i class="fa fa-map"></i> UET Taxila, Pakistan</li>
                </ul>
            </div>
            <div class="col">
                <ul class="nav" style="list-style: none; display: flex; gap: 20px; margin-left:0; padding: 0;">
                    <?php if ($current_user): ?>
                        <?php if ($is_admin): ?>
                            <li><a href="<?php echo smartUrl('event/create.php'); ?>"><i class="fa fa-plus"></i> New Event</a></li>
                        <?php endif; ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa fa-user"></i> <?php echo htmlspecialchars($current_user['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if ($is_admin): ?>
                                    <li><a class="dropdown-item" href="<?php echo smartUrl('admin/index.php'); ?>">Admin Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo smartUrl('sign-out.php'); ?>">Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo smartUrl('sign-in.php'); ?>" style="font-weight: bold;">Sign In</a></li>
                        <li><a href="<?php echo smartUrl('sign-up.php'); ?>" style="font-weight: bold;">Sign Up</a></li>
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
                    <a href="<?php echo smartUrl(); ?>" class="logo">
                        <h2>BRAIN</h2><h3>swarm</h3>
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="<?php echo smartUrl(); ?>" <?php echo ($current_page == 'index') ? 'class="active"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo smartUrl('event/list.php'); ?>" <?php echo (strpos($current_page, 'event') !== false) ? 'class="active"' : ''; ?>>Events</a></li>
                        <li><a href="<?php echo smartUrl('properties.php'); ?>" <?php echo ($current_page == 'properties') ? 'class="active"' : ''; ?>>Our Team</a></li>
                        <li><a href="<?php echo smartUrl('property-details.php'); ?>" <?php echo ($current_page == 'property-details') ? 'class="active"' : ''; ?>>Support</a></li>
                        <li><a href="<?php echo smartUrl('contact.php'); ?>" <?php echo ($current_page == 'contact') ? 'class="active"' : ''; ?>>Contact Us</a></li>
                        <li><a href="<?php echo smartUrl('meeting.php'); ?>" <?php echo ($current_page == 'meeting') ? 'class="active"' : ''; ?>><i class="fa fa-calendar"></i> Schedule a meeting</a></li>
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