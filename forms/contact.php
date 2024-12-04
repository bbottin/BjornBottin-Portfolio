<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $surname = htmlspecialchars(trim($_POST['surname']));
    $number = htmlspecialchars(trim($_POST['number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    $honeypot = trim($_POST['honeypot']); // Honeypot field

    // Validation errors
    $errors = [];

    // Server-side validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($surname)) {
        $errors[] = "Surname is required.";
    }
    if (empty($number) || !preg_match('/^\d+$/', $number)) {
        $errors[] = "Valid contact number is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    }
    if (!empty($honeypot)) {
        $errors[] = "Spam detected.";
    }

    // If no errors, send email
    if (empty($errors)) {
        $to = "info@bjornbottin.co.za"; // Replace with your email
        $headers = "From: $name <$email>\r\nReply-To: $email";
        $emailBody = "Name: $name\nSurname: $surname\nNumber: $number\nEmail: $email\n\nMessage:\n$message";

        if (mail($to, $subject, $emailBody, $headers)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Your message has been sent. Thank you!"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "There was an error sending your message. Please try again later."
            ]);
        }
    } else {
        // Return validation errors
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => implode("<br>", $errors)
        ]);
    }
} else {
    // Invalid request
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed."
    ]);
}
?>
