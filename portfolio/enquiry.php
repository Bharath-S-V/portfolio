<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs and sanitize them
    $fullName = htmlspecialchars(trim($_POST['full-name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone-number']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $budget = htmlspecialchars(trim($_POST['budget']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate inputs
    if (empty($fullName) || empty($email) || empty($subject)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
        exit;
    }

    // Handle file upload if present
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (!empty($_FILES['file']['name'])) {
        // Check if file upload is successful
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $attachment = "<p>Attachment: <a href='$uploadFile'>Download File</a></p>";
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
            exit;
        }
    } else {
        $attachment = '';
    }

    // Prepare email content
    $to = 'svbharath545@gmail.com'; // Replace with your email address
    $subject = "Enquiry from $fullName regarding $subject";
    $messageBody = "
        <html>
        <head>
            <title>Enquiry Form Submission</title>
        </head>
        <body>
            <p><strong>Full Name:</strong> $fullName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone Number:</strong> $phoneNumber</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Budget:</strong> $budget</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
            $attachment
        </body>
        </html>
    ";

    // Set headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // Send email
    if (mail($to, $subject, $messageBody, $headers)) {
        echo json_encode(['status' => 'success', 'message' => 'Your message was sent successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send the message.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
