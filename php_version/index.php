<?php
require_once 'includes/functions.php';

$page_title = 'Brain Swarm - Transforming Educational Experiences';

// Handle contact form submission
$contact_errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Store form data to repopulate form on error
    $form_data = compact('name', 'email', 'subject', 'message');
    
    // Validate form
    if ($error = validateRequired($name, 'Name')) $contact_errors[] = $error;
    if ($error = validateRequired($email, 'Email')) $contact_errors[] = $error;
    if ($error = validateEmailFormat($email)) $contact_errors[] = $error;
    if ($error = validateRequired($subject, 'Subject')) $contact_errors[] = $error;
    if ($error = validateRequired($message, 'Message')) $contact_errors[] = $error;
    
    if (empty($contact_errors)) {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO form_submissions (form_type, name, email, subject, message) VALUES (?, ?, ?, ?, ?)",
                ['home', $name, $email, $subject, $message]
            );
            
            setFlashMessage('success', 'Thank you for your message! We will get back to you soon.');
            redirect(url());
        } catch (Exception $e) {
            $contact_errors[] = 'There was an error submitting your message. Please try again.';
        }
    }
}

// Get latest 3 events for homepage display
try {
    $db = Database::getInstance();
    $latest_events = $db->fetchAll(
        "SELECT e.*, u.username as author_username 
         FROM event e 
         LEFT JOIN users u ON e.author_id = u.id 
         ORDER BY e.created_at DESC 
         LIMIT 3"
    );
} catch (Exception $e) {
    $latest_events = [];
}

// Start output buffering for content
ob_start();
?>

<section class="hero-section" id="section_1">
    <div class="section-overlay"></div>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="row">

            <div class="col-12 mt-auto mb-5 text-center">
                <small>TRANSFORMING</small>

                <h1 style="color: red;">EDUCATIONAL EXPERIENCES</h1>
                <small>ACCELERATING INNOVATIVE RESEARCH</small>
                <br>

                <a class="btn custom-btn smoothscroll mt-5" href="<?php echo smartUrl('property-details.php'); ?>">Let's begin</a>
            </div>

            <div class="col-lg-12 col-12 mt-auto d-flex flex-column flex-lg-row text-center">
                <div class="date-wrap">
                    <h5 class="text-white">
                        <i class="custom-icon bi-clock me-2"></i>
                        24/7 Availability
                    </h5>
                </div>

                <div class="location-wrap mx-auto py-3 py-lg-0">
                    <h5 class="text-white">
                        <i class="custom-icon bi-geo-alt me-2"></i>
                        UET Taxila, Pakistan
                    </h5>
                </div>

                <div class="social-share">
                    <ul class="social-icon d-flex align-items-center justify-content-center">
                        <span class="text-white me-3">Share:</span>

                        <li class="social-icon-item">
                            <a href="#" class="social-icon-link">
                                <span class="bi-facebook"></span>
                            </a>
                        </li>

                        <li class="social-icon-item">
                            <a href="#" class="social-icon-link">
                                <span class="bi-twitter"></span>
                            </a>
                        </li>

                        <li class="social-icon-item">
                            <a href="#" class="social-icon-link">
                                <span class="bi-instagram"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="video-wrap">
        <video autoplay="" loop="" muted="" class="custom-video" poster="">
            <source src="<?php echo smartAsset('video/pexels-2022395.mp4'); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</section>

<div class="featured section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="left-image">
                    <img src="<?php echo smartAsset('assets/images/featured.jpg'); ?>" alt="">
                    <a href="<?php echo smartUrl('property-details.php'); ?>"><img src="<?php echo smartAsset('assets/images/featured-icon.png'); ?>" alt="" style="max-width: 60px; padding: 0px;"></a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section-heading">
                    <h6>| Featured</h6>
                    <h2>ACADEMIA IS IN OUR DNA</h2>
                </div>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How does this work ?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>BRAIN-SWARM</strong> partners with global universities, business organizations and professional associations to understand the diverse and evolving needs of the academic engineering community.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Aligned to modern industrial and academic trends
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                We offer turnkey solutions to ensure that your systems provide value to the most ambitious and cutting-edge researchers and educators who are impacting generations of students.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                An R&D team of specialized Academic Applications Engineers
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Dedicated to developing solutions for advanced teaching, research and learning outcomes. Our platforms include curricula support, courseware and content resources, as well as advanced application examples for researchers.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="info-table">
                    <ul>
                        <li>
                            <img src="<?php echo smartAsset('assets/images/info-icon-01.png'); ?>" alt="Lab Space Icon" style="max-width: 52px;">
                            <h4>Lab-Ready<br><span>Turnkey Academic</span></h4>
                        </li>
                        <li>
                            <img src="<?php echo smartAsset('assets/images/info-icon-02.png'); ?>" alt="Partnership Icon" style="max-width: 52px;">
                            <h4>Collaboration<br><span>Partnered Universities</span></h4>
                        </li>
                        <li>
                            <img src="<?php echo smartAsset('assets/images/info-icon-03.png'); ?>" alt="Scalability Icon" style="max-width: 52px;">
                            <h4>Scalable<br><span>Cross-Department</span></h4>
                        </li>
                        <li>
                            <img src="<?php echo smartAsset('assets/images/info-icon-04.png'); ?>" alt="Support Icon" style="max-width: 52px;">
                            <h4>Academic<br><span>R&D Expertise</span></h4>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="video section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 offset-lg-4">
                <div class="section-heading text-center">
                    <h6>| Video Demo</h6>
                    <h2>Experience BrainSwarm In Action</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="video-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="video-frame">
                    <img src="<?php echo smartAsset('assets/images/video-frame.jpg'); ?>" alt="">
                    <a href="https://www.facebook.com/BrainSwarming/" target="_blank"><i class="fa fa-play"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fun-facts">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="34" data-speed="1000"></h2>
                                <p class="count-text">Universities<br>Using Our Kits</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="12" data-speed="1000"></h2>
                                <p class="count-text">Years<br>Innovation & R&D</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="24" data-speed="1000"></h2>
                                <p class="count-text">Awards<br>in Robotics & AI</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section best-deal">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5">
                <div class="section-heading">
                    <h6>| Best Deal</h6>
                    <h2>Find Your Best Deal</h2>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="tabs-content">
                    <div class="row">
                        <div class="nav-wrapper">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="swarm-tab" data-bs-toggle="tab" data-bs-target="#swarm" type="button" role="tab" aria-controls="swarm" aria-selected="true">Swarm Kit</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="drone-tab" data-bs-toggle="tab" data-bs-target="#drone" type="button" role="tab" aria-controls="drone" aria-selected="false">Aerial Drone</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="arm-tab" data-bs-toggle="tab" data-bs-target="#arm" type="button" role="tab" aria-controls="arm" aria-selected="false">Robotic Arm</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="swarm" role="tabpanel" aria-labelledby="swarm-tab">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="info-table">
                                            <ul>
                                                <li>Max Agents <span>20+</span></li>
                                                <li>Connectivity <span>Mesh Wi-Fi</span></li>
                                                <li>Battery Life <span>6 hours</span></li>
                                                <li>Sensor Suite <span>Lidar, IMU, IR</span></li>
                                                <li>Programming <span>Python, ROS</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <img src="<?php echo smartAsset('assets/images/single1-property.jpg'); ?>" alt="">
                                    </div>
                                    <div class="col-lg-3">
                                        <h4>Swarm Robotics Development Kit</h4>
                                        <p>Our most advanced swarm solution for AI experimentation, pathfinding, collective behavior, and coordination studies. Used by top research labs worldwide.</p>
                                        <div class="icon-button">
                                            <a href="<?php echo smartUrl('meeting.php'); ?>"><i class="fa fa-calendar"></i> Request a Demo</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="drone" role="tabpanel" aria-labelledby="drone-tab">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="info-table">
                                            <ul>
                                                <li>Flight Time <span>25 mins</span></li>
                                                <li>Range <span>2 km</span></li>
                                                <li>Camera <span>4K with stabilization</span></li>
                                                <li>Autonomy <span>GPS + Vision</span></li>
                                                <li>Control <span>Mobile + PC</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <img src="<?php echo smartAsset('assets/images/single2.jpg'); ?>" alt="">
                                    </div>
                                    <div class="col-lg-3">
                                        <h4>AI-Powered Aerial Drone Kit</h4>
                                        <p>Fully integrated drone platform for aerial data collection, autonomous flight path testing, and embedded vision research. Comes with simulation support and SDKs.</p>
                                        <div class="icon-button">
                                            <a href="<?php echo smartUrl('meeting.php'); ?>"><i class="fa fa-calendar"></i> Request a Demo</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="arm" role="tabpanel" aria-labelledby="arm-tab">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="info-table">
                                            <ul>
                                                <li>DOF <span>6-axis</span></li>
                                                <li>Reach <span>850mm</span></li>
                                                <li>Repeatability <span>±0.1mm</span></li>
                                                <li>End-Effector <span>Gripper + Camera</span></li>
                                                <li>OS Support <span>Windows, Linux</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <img src="<?php echo smartAsset('assets/images/single3.jpg'); ?>" alt="">
                                    </div>
                                    <div class="col-lg-3">
                                        <h4>Precision Robotic Arm Platform</h4>
                                        <p>Engineered for teaching and research in robotics control, computer vision, and kinematics. Built-in curriculum available for academic use.</p>
                                        <div class="icon-button">
                                            <a href="<?php echo smartUrl('meeting.php'); ?>"><i class="fa fa-calendar"></i> Request a Demo</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- tab-content -->
                    </div> <!-- row -->
                </div> <!-- tabs-content -->
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

<div class="events section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 offset-lg-4">
                <div class="section-heading text-center">
                    <h6>| Latest Events</h6>
                    <h2>Recent Events & Updates</h2>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <?php if (!empty($latest_events)): ?>
                <?php foreach ($latest_events as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm event-card">
                            <?php if (!empty($event['image'])): ?>
                                <img src="<?php echo smartUrl('uploads/event_images/' . $event['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                     class="card-img-top">
                            <?php else: ?>
                                <div class="card-img-top placeholder-img d-flex align-items-center justify-content-center">
                                    <i class="fa fa-calendar" style="font-size: 3rem; color: #ff6b6b;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <p class="card-text flex-grow-1">
                                    <?php 
                                    // Truncate content to show excerpt
                                    $excerpt = strlen($event['content']) > 100 
                                        ? substr($event['content'], 0, 100) . '...' 
                                        : $event['content'];
                                    echo htmlspecialchars($excerpt);
                                    ?>
                                </p>
                                <div class="mt-auto">
                                    <small class="text-muted mb-2 d-block">
                                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($event['author_username']); ?> • 
                                        <i class="fa fa-calendar"></i> <?php echo formatDate($event['publish_date'], 'M d, Y'); ?>
                                    </small>
                                    <a href="<?php echo smartUrl('event/detail.php?id=' . $event['id']); ?>" 
                                       class="btn btn-primary w-100">View Event</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-calendar" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <h4 class="text-muted">No events yet</h4>
                        <p class="text-muted">Stay tuned for upcoming events and updates!</p>
                        <?php if (SessionManager::isAdmin()): ?>
                            <a href="<?php echo smartUrl('event/create.php'); ?>" class="btn btn-primary mt-3">Create First Event</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?php echo smartUrl('event/list.php'); ?>" class="btn btn-outline-primary">View All Events</a>
            </div>
        </div>
    </div>
</div>

<div class="contact section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 offset-lg-4">
                <div class="section-heading text-center">
                    <h6>| Contact Us</h6>
                    <h2>Get In Touch With Our Agents</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div id="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3316.7306723433326!2d72.82072417495658!3d33.76762493270668!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38dfa436e082015d%3A0xf729c78e5ac28d57!2sUniversity%20of%20Engineering%20and%20Technology%20(UET)%2C%20Taxila!5e0!3m2!1sen!2s!4v1752158790528!5m2!1sen!2s" width="100%" height="500px" frameborder="0" style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" allowfullscreen=""></iframe>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="item phone">
                            <img src="<?php echo smartAsset('assets/images/phone-icon.png'); ?>" alt="" style="max-width: 52px;">
                            <h6>010-020-0340<br><span>Phone Number</span></h6>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="item email">
                            <img src="<?php echo smartAsset('assets/images/email-icon.png'); ?>" alt="" style="max-width: 52px;">
                            <h6>info@villa.co<br><span>Business Email</span></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <form id="contact-form" method="post">
                    <?php if (!empty($contact_errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($contact_errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="name">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Your Name..." 
                                       value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required autocomplete="on">
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Your E-mail..." 
                                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required pattern="[^ @]*@[^ @]*">
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject..." 
                                       value="<?php echo htmlspecialchars($form_data['subject'] ?? ''); ?>" autocomplete="on">
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="message">Message</label>
                                <textarea name="message" id="message" class="form-control" placeholder="Your Message" rows="5"><?php echo htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" id="form-submit" class="orange-button">Send Message</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Events Section -->
<style>
.events.section {
    padding: 100px 0;
    background-color: #f8f9fa;
}

.event-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.15);
}

.event-card .card-img-top {
    height: 200px;
    object-fit: cover;
}

.event-card .placeholder-img {
    height: 200px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.event-card .card-title {
    color: #333;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.event-card .card-text {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.6;
}

.event-card .btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.event-card .btn-primary:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
    transform: translateY(-2px);
}

.btn-outline-primary {
    border-color: #ff6b6b;
    color: #ff6b6b;
    border-radius: 25px;
    padding: 12px 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background-color: #ff6b6b;
    border-color: #ff6b6b;
    color: white;
    transform: translateY(-2px);
}

.events .section-heading h6 {
    color: #ff6b6b;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.events .section-heading h2 {
    color: #333;
    font-weight: 700;
    margin-bottom: 2rem;
}

.text-muted i {
    margin-right: 5px;
}

@media (max-width: 768px) {
    .events.section {
        padding: 60px 0;
    }
    
    .event-card .card-img-top,
    .event-card .placeholder-img {
        height: 180px;
    }
}
</style>

<?php
// Get the content
$content = ob_get_clean();

// Include the enhanced base template
include 'templates/base_enhanced.php';
?>