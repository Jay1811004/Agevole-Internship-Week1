<?php
// Start session at the beginning of the file
session_start();

// Initialize variables for messages
$errorMsg = '';

// Display success message from session if it exists
$successMsg = '';
if (isset($_SESSION['success_message'])) {
    $successMsg = $_SESSION['success_message'];
    // Clear the message so it won't appear on refresh
    unset($_SESSION['success_message']);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize inputs
    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Validate inputs
    $errors = [];
    
    // Check for empty fields
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, save data to CSV file
    if (empty($errors)) {
        // Format data for saving
        $formData = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'date' => date('Y-m-d H:i:s')
        ];
        
        // CSV Format
        $filePath = 'contact_submissions.csv';
        $fileExists = file_exists($filePath);
        
        // Open file in append mode
        $file = fopen($filePath, 'a');
        
        // Add headers if file is new
        if (!$fileExists) {
            fputcsv($file, array_keys($formData));
        }
        
        // Add data row
        fputcsv($file, $formData);
        fclose($file);
        
        // Store success message in session
        $_SESSION['success_message'] = "Thank you for your message! We'll get back to you soon.";
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Join errors with line breaks
        $errorMsg = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .messages {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mb-4 text-center">Contact Us</h2>
            
            <!-- Messages container for displaying success/error messages -->
            <div class="messages">
                <?php if(!empty($successMsg)): ?>
                    <div class="alert alert-success"><?php echo $successMsg; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($errorMsg)): ?>
                    <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Contact Form -->
            <form id="contact-form" method="post" action="">
                <div class="row mb-3">
                    <div class="col">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required data-error="Please enter your name">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required data-error="Please enter a valid email address">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required data-error="Please enter a subject">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required data-error="Please enter your message"></textarea>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Form validation script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
</body>
</html>
