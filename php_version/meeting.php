<?php
require_once 'includes/functions.php';

$page_title = 'Schedule a Meeting - Brain Swarm';

// Handle meeting form submission
$meeting_errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $meeting_purpose = sanitizeInput($_POST['meeting_purpose'] ?? '');
    $preferred_date = sanitizeInput($_POST['preferred_date'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Store form data to repopulate form on error
    $form_data = compact('name', 'email', 'phone', 'meeting_purpose', 'preferred_date', 'message');
    
    // Validate form
    if ($error = validateRequired($name, 'Name')) $meeting_errors[] = $error;
    if ($error = validateRequired($email, 'Email')) $meeting_errors[] = $error;
    if ($error = validateEmailFormat($email)) $meeting_errors[] = $error;
    if ($error = validateRequired($phone, 'Phone')) $meeting_errors[] = $error;
    if ($error = validateRequired($meeting_purpose, 'Meeting Purpose')) $meeting_errors[] = $error;
    if ($error = validateRequired($preferred_date, 'Preferred Date')) $meeting_errors[] = $error;
    
    // Validate meeting purpose
    $valid_purposes = ['buy_kit', 'custom_project'];
    if (!in_array($meeting_purpose, $valid_purposes)) {
        $meeting_errors[] = 'Please select a valid meeting purpose.';
    }
    
    if (empty($meeting_errors)) {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO form_submissions (form_type, name, email, phone, meeting_purpose, preferred_date, message) VALUES (?, ?, ?, ?, ?, ?, ?)",
                ['meeting', $name, $email, $phone, $meeting_purpose, $preferred_date, $message]
            );
            
            setFlashMessage('success', 'Thank you for scheduling a meeting! We will contact you to confirm the details.');
            redirect(smartUrl('meeting.php'));
        } catch (Exception $e) {
            $meeting_errors[] = 'There was an error submitting your meeting request. Please try again.';
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
                <span class="breadcrumb"><a href="<?php echo url(); ?>">Home</a> / Meeting</span>
                <h3>Schedule a meeting</h3>
            </div>
        </div>
    </div>
</div>

<section class="ticket-section section-padding">
    <div class="section-overlay"></div>

    <div class="container">
        <div class="row">

            <div class="col-lg-6 col-10 mx-auto">
                <form class="custom-form ticket-form mb-5 mb-lg-0" method="POST" role="form">
                    <h2 class="text-center mb-4">Schedule a Meeting with Brain Swarm</h2>

                    <?php if (!empty($meeting_errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($meeting_errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="ticket-form-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <input type="text" name="name" id="name" class="form-control" 
                                       placeholder="Your Full Name" required 
                                       value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>">
                            </div>

                            <div class="col-lg-6 col-md-6 col-12">
                                <input type="email" name="email" id="email" class="form-control" 
                                       placeholder="Your Email Address" required pattern="[^ @]*@[^ @]*"
                                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <input type="text" name="phone" id="phone" class="form-control" 
                               placeholder="Phone (e.g. 0300-123-4567)" pattern="[0-9]{4}-[0-9]{3}-[0-9]{4}" required
                               value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">

                        <h6>Meeting Purpose</h6>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-check form-control">
                                    <input class="form-check-input" type="radio" name="meeting_purpose" 
                                           id="flexRadioDefault1" value="buy_kit" 
                                           <?php echo (($form_data['meeting_purpose'] ?? '') === 'buy_kit') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Buy Robotics Kit
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-check form-check-radio form-control">
                                    <input class="form-check-input" type="radio" name="meeting_purpose" 
                                           id="flexRadioDefault2" value="custom_project"
                                           <?php echo (($form_data['meeting_purpose'] ?? '') === 'custom_project') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Discuss Custom Project
                                    </label>
                                </div>
                            </div>
                        </div>

                        <input type="text" name="preferred_date" id="preferred_date" class="form-control" 
                               placeholder="Preferred Meeting Date (e.g. 2025-07-12)" required
                               value="<?php echo htmlspecialchars($form_data['preferred_date'] ?? ''); ?>">

                        <textarea name="message" rows="3" class="form-control" id="message" 
                                  placeholder="Any specific requirements or notes?"><?php echo htmlspecialchars($form_data['message'] ?? ''); ?></textarea>

                        <div class="col-lg-4 col-md-10 col-8 mx-auto">
                            <button type="submit" class="form-control">Schedule</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<?php
// Get the content
$content = ob_get_clean();

// Include the base template
include 'templates/base_enhanced.php';
?>