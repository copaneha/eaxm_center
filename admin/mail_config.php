<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'neha731784@gmail.com';
    $mail->Password   = 'bjxcuvmvgdedekia'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('neha731784@gmail.com', 'Exam Center Admin');
} catch (Exception $e) { }

// Approval Email
function send_credential_mail($toEmail, $userName, $rollNo, $password) {
    global $mail; 
    try {
        $mail->clearAddresses();
        $mail->addAddress($toEmail, $userName);
        $mail->isHTML(true);
        $mail->Subject = "Admission Approved - Login Details";
        $mail->Body = "<h2>Hello $userName,</h2><p>Your admission is approved.</p>
                       <p><b>Roll No:</b> $rollNo <br> <b>Password:</b> $password</p>";
        return $mail->send();
    } catch (Exception $e) { return false; }
}

// Rejection Email
function send_rejection_mail($toEmail, $userName, $reason) {
    global $mail; 
    try {
        $mail->clearAddresses();
        $mail->addAddress($toEmail, $userName);
        $mail->isHTML(true);
        $mail->Subject = "Application Update: Rejected";
        $mail->Body = "<h2>Hello $userName,</h2>
                       <p>We regret to inform you that your application has been rejected.</p>
                       <p><b>Reason:</b> $reason</p>";
        return $mail->send();
    } catch (Exception $e) { return false; }
}
?>