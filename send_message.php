<?php
include "config.php";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // 1. Save to Database for Admin
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($conn->query($sql)) {
        // 2. Send Email to Admin
        $to = "admin@yourproject.com"; // Apni email yaha dalein
        $headers = "From: " . $email;
        $body = "New Message from: $name\n\nMessage: $message";
        
        mail($to, $subject, $body, $headers); // Note: Localhost pe mail server setup hona chahiye

        echo "<script>alert('Message sent successfully!'); window.location='contact.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>