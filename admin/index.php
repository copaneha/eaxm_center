<?php
session_start();
include "../config.php";
include "mail_config.php"; 

$error = "";
$success = "";
$step = isset($_POST['step']) ? $_POST['step'] : 'login'; 

// --- CANCEL LOGIC ---
if(isset($_POST['btn_cancel'])){
    unset($_SESSION['temp_otp'], $_SESSION['temp_email'], $_SESSION['otp_time']);
    $step = 'login';
}

// --- 1. LOGIN LOGIC ---
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); 
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='admin'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) == 1){
        $_SESSION['email'] = $email;
        $_SESSION['admin_logged_in'] = true;
        header("Location:dascboard.php");
        exit();
    } else { $error = "Invalid Admin Credentials!"; }
}

// --- 2. SEND OTP LOGIC ---
if(isset($_POST['send_otp'])){
    $email = mysqli_real_escape_string($conn, $_POST['reset_email']);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='admin'");
    
    if(mysqli_num_rows($res) > 0){
        $otp = rand(100000, 999999);
        $_SESSION['temp_otp'] = $otp;
        $_SESSION['temp_email'] = $email;
        $_SESSION['otp_time'] = time();

        try {
            $mail->addAddress($email); 
            $mail->isHTML(true);
            $mail->Subject = 'Security OTP - Admin Panel';
            $mail->Body    = "<h3>Your OTP is: <b>$otp</b></h3><p>Valid for 2 minutes only.</p>";

            if($mail->send()){
                $success = "OTP sent successfully!";
                $step = 'verify_otp';
            } else { $error = "Mailer Error!"; $step = 'forgot'; }
        } catch (Exception $e) { $error = "Error: {$mail->ErrorInfo}"; $step = 'forgot'; }
    } else { $error = "Email not found!"; $step = 'forgot'; }
}

// --- 3. VERIFY OTP LOGIC ---
if(isset($_POST['check_otp'])){
    $current_time = time();
    if(!isset($_SESSION['otp_time']) || ($current_time - $_SESSION['otp_time']) > 120){
        $error = "OTP Expired! Please try again.";
        unset($_SESSION['temp_otp'], $_SESSION['otp_time']);
        $step = 'forgot';
    } else {
        if($_POST['otp'] == $_SESSION['temp_otp']){
            $step = 'new_password';
        } else { 
            $error = "Invalid OTP code!"; 
            $step = 'verify_otp'; 
        }
    }
}

// --- 4. UPDATE PASSWORD LOGIC (Updated with Confirm Password check) ---
if(isset($_POST['update_pass'])){
    $new_p = $_POST['n_pass'];
    $conf_p = $_POST['c_pass'];

    if($new_p !== $conf_p){
        $error = "Galti: Passwords match nahi ho rahe!";
        $step = 'new_password';
    } else {
        $hashed_p = md5($new_p);
        $email = $_SESSION['temp_email'];
        if(mysqli_query($conn, "UPDATE users SET password='$hashed_p' WHERE email='$email'")){
            $success = "Password updated! Login now.";
            unset($_SESSION['temp_otp'], $_SESSION['temp_email'], $_SESSION['otp_time']);
            $step = 'login';
        } else { $error = "Database Error!"; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Online Exam Center</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    body, html { margin: 0; padding: 0; height: 100%; font-family: 'Segoe UI', sans-serif; }
    body { background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('images/background.png'); background-size: cover; background-position: center; display: flex; flex-direction: column; }
    .header-section { background: linear-gradient(to bottom, #1565c0, #1976d2); color: white; text-align: center; padding: 20px 0; clip-path: ellipse(150% 100% at 50% 0%); }
    .main-container { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
    .login-card { background: white; width: 100%; max-width: 400px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; text-align: center; padding: 30px; box-sizing: border-box; }
    .otp-highlight { background: #f0f7ff; padding: 20px; border-radius: 15px; border: 1px dashed #1976d2; margin-bottom: 15px; }
    .input-group { position: relative; margin-bottom: 20px; text-align: left; }
    .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #1976d2; }
    .input-group input { width: 100%; padding: 14px 15px 14px 45px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
    .login-btn { width: 100%; padding: 15px; background: #1976d2; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
    .login-btn:hover { background: #1565c0; box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3); }
    .cancel-btn { width: 100%; padding: 12px; background: #f1f1f1; color: #555; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: 0.3s; margin-bottom: 10px; }
    .cancel-btn:hover { background: #e0e0e0; }
    .back-home-btn { margin-top: 15px; padding: 12px; background: transparent; color: #1976d2; border: 2px solid #1976d2; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; width: 100%; box-sizing: border-box; font-size: 14px; }
    .error-msg { background: #fdecea; color: red; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid red; text-align: left; }
    .success-msg { background: #e7f9ed; color: green; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid green; text-align: left; }
    .footer-section { background: #0f20dd; color: white; padding: 15px; text-align: center; font-size: 13px; width: 100%; }
    #otp-timer { color: #d32f2f; font-weight: bold; font-size: 1.1em; }
</style>
</head>
<body>

<div class="header-section">
    <i class="fas fa-user-shield" style="font-size: 50px;"></i>
    <h1>Admin Control Panel</h1>
</div>

<div class="main-container">
    <div class="login-card">
        
        <?php if($error!=""){ echo "<div class='error-msg'>$error</div>"; } ?>
        <?php if($success!=""){ echo "<div class='success-msg'>$success</div>"; } ?>

        <?php if($step == 'login'){ ?>
            <h2>Administrator Login</h2>
            <form method="POST">
                <input type="hidden" name="step" value="login">
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="Admin Email" required></div>
                <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Password" required></div>
                <button type="submit" name="login" class="login-btn">ACCESS DASHBOARD</button>
            </form>
            <form method="POST">
                <input type="hidden" name="step" value="forgot">
                <button type="submit" style="background:none; border:none; color:#1976d2; cursor:pointer; margin-top:5px; font-weight:600; text-decoration:underline;">Forgot Password?</button>
            </form>

        <?php } elseif($step == 'forgot'){ ?>
            <h2>Reset Password</h2>
            <p style="color:#666; margin-bottom:20px;">Enter your registered email to receive a 6-digit OTP.</p>
            <form method="POST">
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="reset_email" placeholder="Enter Admin Email" required></div>
                <button type="submit" name="send_otp" class="login-btn">SEND OTP</button>
                <button type="submit" name="btn_cancel" class="cancel-btn">CANCEL</button>
            </form>

        <?php } elseif($step == 'verify_otp'){ 
            $time_left = 120 - (time() - $_SESSION['otp_time']);
            $time_left = ($time_left < 0) ? 0 : $time_left;
        ?>
            <div class="otp-highlight">
                <h2 style="margin-top:0;">Verify OTP</h2>
                <p>Time remaining: <span id="otp-timer">02:00</span></p>
                <form method="POST" id="otp-form">
                    <div class="input-group">
                        <i class="fas fa-key"></i>
                        <input type="text" name="otp" id="otp-input" placeholder="Enter 6-Digit OTP" required maxlength="6" autocomplete="off">
                    </div>
                    <button type="submit" name="check_otp" id="verify-btn" class="login-btn">VERIFY CODE</button>
                    <button type="submit" name="btn_cancel" class="cancel-btn">CANCEL</button>
                </form>
            </div>

            <script>
                var secondsLeft = <?php echo $time_left; ?>;
                var timerDisplay = document.getElementById('otp-timer');
                function updateTimer() {
                    var minutes = Math.floor(secondsLeft / 60);
                    var seconds = secondsLeft % 60;
                    timerDisplay.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
                    if (secondsLeft <= 0) {
                        clearInterval(timerInterval);
                        timerDisplay.innerHTML = "EXPIRED";
                    }
                    secondsLeft--;
                }
                var timerInterval = setInterval(updateTimer, 1000);
                updateTimer();
            </script>

        <?php } elseif($step == 'new_password'){ ?>
            <h2>Set New Password</h2>
            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="n_pass" placeholder="Enter New Password" required minlength="6">
                </div>
                <div class="input-group">
                    <i class="fas fa-check-double"></i>
                    <input type="password" name="c_pass" placeholder="Confirm New Password" required minlength="6">
                </div>
                
                <button type="submit" name="update_pass" class="login-btn">UPDATE PASSWORD</button>
                <button type="submit" name="btn_cancel" class="cancel-btn">CANCEL</button>
            </form>
        <?php } ?>

        <a href="../index.php" class="back-home-btn">
            <i class="fas fa-arrow-left"></i> RETURN TO MAIN LOGIN
        </a>
    </div>
</div>

<footer class="footer-section">&copy; 2026 <strong>Online Exam Center</strong>. All Rights Reserved.</footer>
</body>
</html>