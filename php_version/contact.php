<?php
require_once 'includes/functions.php';

$page_title = 'Contact Us - Brain Swarm';

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
                ['contact', $name, $email, $subject, $message]
            );
            
            setFlashMessage('success', 'Thank you for contacting us! We will respond to your inquiry soon.');
            redirect(smartUrl('contact.php'));
        } catch (Exception $e) {
            $contact_errors[] = 'There was an error submitting your message. Please try again.';
        }
    }
}

// Start output buffering for content
ob_start();
?>

<div class="page-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <span class="breadcrumb"><a href="<?php echo url(); ?>">Home</a> / Contact Us</span>
                <h3>Contact Us</h3>
            </div>
        </div>
    </div>
</div>

<div class="contact-page section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-heading">
                    <h6>| Contact Us</h6>
                    <h2>Get In Touch With Our Agents</h2>
                </div>
                <div class="section-heading">
                    Let us know how we can support your project or research.
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="item phone">
                            <img src="<?php echo smartAsset('assets/images/phone-icon.png'); ?>" alt="" style="max-width: 52px;">
                            <h6>010-020-0340<br><span>Phone Number</span></h6>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="item email">
                            <img src="<?php echo smartAsset('assets/images/email-icon.png'); ?>" alt="" style="max-width: 52px;">
                            <h6>info@villa.co<br><span>Business Email</span></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
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
                                <textarea name="message" id="message" class="form-control" placeholder="Your Message" rows="6"><?php echo htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
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
        <div class="row">
            <div class="col-lg-12">
                <div id="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3316.7306723433326!2d72.82072417495658!3d33.76762493270668!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38dfa436e082015d%3A0xf729c78e5ac28d57!2sUniversity%20of%20Engineering%20and%20Technology%20(UET)%2C%20Taxila!5e0!3m2!1sen!2s!4v1752158790528!5m2!1sen!2s" 
                            width="100%" height="500px" frameborder="0" 
                            style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" 
                            allowfullscreen=""></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content
$content = ob_get_clean();

// Include the base template
include 'templates/base.php';
?>