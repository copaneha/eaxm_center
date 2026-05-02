<?php
// 1. Database and Mail Configuration Files include karein
include "../config.php";      // Database connection ke liye
include "mail_config.php";  // PHPMailer setup aur SMTP settings ke liye

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $msg_id = $_POST['msg_id'];
    $user_email = $_POST['user_email'];
    $reply_msg = $conn->real_escape_string($_POST['reply_msg']);

    // 2. Database Update Logic
    $update_sql = "UPDATE contact_messages 
                   SET admin_reply = '$reply_msg', status = 'Replied' 
                   WHERE id = $msg_id";

    if ($conn->query($update_sql) === TRUE) {
        
        // 3. PHPMailer Logic (Assuming $mail object is created in mailconfig.php)
        try {
            // Recipient (Kise bhejna hai)
            $mail->addAddress($user_email); 

            // Content
            $mail->isHTML(true);                                  
            $mail->Subject = 'Reply from HAME Institute Support';
            $mail->Body    = "
                <h3>Hello,</h3>
                <p>Admin has replied to your query:</p>
                <div style='background:#f4f4f4; padding:15px; border-left:4px solid #28a745;'>
                    <strong>Reply:</strong> $reply_msg
                </div>
                <br>
                <p>Thank you for contacting <b>HAME Institute</b>.</p>
            ";

            // Email Send Karein
            if($mail->send()) {
                echo "<script>alert('Reply sent via Email and saved in Database!'); window.location='admin_inbox.php';</script>";
            } else {
                echo "<script>alert('Database updated, but Email could not be sent.'); window.location='admin_inbox.php';</script>";
            }

        } catch (Exception $e) {
            echo "<script>alert('Message saved, but Mailer Error: {$mail->ErrorInfo}'); window.location='admin_inbox.php';</script>";
        }

    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>