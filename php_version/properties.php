<?php
require_once 'includes/functions.php';

$page_title = 'Our Team - Brain Swarm';

// Start output buffering for content
ob_start();
?>

<div class="page-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <span class="breadcrumb"><a href="<?php echo url(); ?>">Home</a> / Our Team</span>
                <h3>Our Team</h3>
            </div>
        </div>
    </div>
</div>

<div class="properties section">
    <section class="artists-section section-padding" id="section_3">
        <div class="container">
            <div class="row justify-content-center">

                <div class="col-12 text-center">
                    <h2 class="mb-4">OUR TEAM</h2>
                </div>

                <div class="col-lg-5 col-12">
                    <div class="artists-thumb">
                        <div class="artists-image-wrap">
                            <img src="<?php echo smartAsset('images/artists/joecalih-UmTZqmMvQcw-unsplash.jpg'); ?>" class="artists-image img-fluid">
                        </div>

                        <div class="artists-hover">
                            <p>
                                <strong>Name:</strong>
                                Madona
                            </p>

                            <p>
                                <strong>Birthdate:</strong>
                                August 16, 1958
                            </p>

                            <p>
                                <strong>Music:</strong>
                                Pop, R&amp;B
                            </p>

                            <hr>

                            <p class="mb-0">
                                <strong>Youtube Channel:</strong>
                                <a href="#">Madona Official</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-12">
                    <div class="artists-thumb">
                        <div class="artists-image-wrap">
                            <img src="<?php echo smartAsset('images/artists/abstral-official-bdlMO9z5yco-unsplash.jpg'); ?>" class="artists-image img-fluid">
                        </div>

                        <div class="artists-hover">
                            <p>
                                <strong>Name:</strong>
                                Rihana
                            </p>

                            <p>
                                <strong>Birthdate:</strong>
                                Feb 20, 1988
                            </p>

                            <p>
                                <strong>Music:</strong>
                                Country
                            </p>

                            <hr>

                            <p class="mb-0">
                                <strong>Youtube Channel:</strong>
                                <a href="#">Rihana Official</a>
                            </p>
                        </div>
                    </div>

                    <div class="artists-thumb">
                        <img src="<?php echo smartAsset('images/artists/soundtrap-rAT6FJ6wltE-unsplash.jpg'); ?>" class="artists-image img-fluid">

                        <div class="artists-hover">
                            <p>
                                <strong>Name:</strong>
                                Bruno Bros
                            </p>

                            <p>
                                <strong>Birthdate:</strong>
                                October 8, 1985
                            </p>

                            <p>
                                <strong>Music:</strong>
                                Pop
                            </p>

                            <hr>

                            <p class="mb-0">
                                <strong>Youtube Channel:</strong>
                                <a href="#">Bruno Official</a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php
// Get the content
$content = ob_get_clean();

// Include the base template
include 'templates/base_enhanced.php';
?>