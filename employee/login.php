<?php
session_start();
// File paths safety
if (file_exists("../config.php")) { include "../config.php"; } else { include "config.php"; }
if (file_exists("../admin/mail_config.php")) { include "../admin/mail_config.php"; }

$error = "";
$success = "";
$step = $_POST['step'] ?? 'login';

// --- 1. LOGIN LOGIC (FIXED) ---
if(isset($_POST['emp_login'])){
    // Trim spaces and escape
    $email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
    $pass = trim($_POST['password'] ?? '');
    
    $res = mysqli_query($conn, "SELECT * FROM employees WHERE email='$email'");
    
    if(mysqli_num_rows($res) == 1){
        $row = mysqli_fetch_assoc($res);
        $db_pass = $row['password'];

        // Multi-level Password Verification
        $is_valid = false;
        if (password_verify($pass, $db_pass)) {
            $is_valid = true; // BCRYPT
        } elseif (md5($pass) === $db_pass) {
            $is_valid = true; // MD5
        } elseif ($pass === $db_pass) {
            $is_valid = true; // Plain Text
        }

        if($is_valid){
            $_SESSION['emp_id'] = $row['id'];
            $_SESSION['emp_name'] = $row['name'];
            $_SESSION['emp_logged_in'] = true;
            header("Location: employee_dashboard.php");
            exit();
        } else { 
            $error = "Galti: Password sahi nahi hai!"; 
        }
    } else { 
        $error = "Galti: Yeh Email registered nahi hai!"; 
    }
}

// --- 2. SEND OTP LOGIC ---
if(isset($_POST['send_otp'])){
    $email = mysqli_real_escape_string($conn, trim($_POST['reset_email']));
    $res = mysqli_query($conn, "SELECT * FROM employees WHERE email='$email'");

    if(mysqli_num_rows($res) > 0){
        $otp = rand(100000,999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['time'] = time();

        if(isset($mail)){
            try {
                $mail->clearAddresses();
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Security OTP - Employee Portal";
                $mail->Body = "<div style='font-family:sans-serif; padding:20px; border:1px solid #eee;'>
                                <h2 style='color:#1e3a8a;'>Password Reset Request</h2>
                                <p>Aapka security OTP hai: <b style='color:#2563eb; font-size:24px;'>$otp</b></p>
                                <p>Yeh sirf 2 minute tak valid hai.</p>
                               </div>";
                if($mail->send()){
                    $success = "OTP aapki email par bhej diya gaya hai!";
                    $step = "verify_otp";
                } else { $error = "Mail bhenjne mein error!"; }
            } catch (Exception $e) { $error = "Mailer Error!"; }
        } else { $error = "Mail configuration missing!"; }
    } else { $error = "Email database mein nahi mila!"; }
}

// --- 3. VERIFY OTP ---
if(isset($_POST['check_otp'])){
    if((time() - ($_SESSION['time'] ?? 0)) > 120){ 
        $error = "OTP Expire ho gaya! Dobara koshish karein.";
        $step = "forgot";
    } else {
        if(($_POST['otp'] ?? '') == ($_SESSION['otp'] ?? '')){
            $step = "new_password";
        } else { $error = "Galat OTP! Sahi code dalein."; $step = "verify_otp"; }
    }
}

// --- 4. UPDATE PASSWORD ---
if(isset($_POST['update_pass'])){
    $new_pass = $_POST['new_pass'] ?? '';
    $conf_pass = $_POST['conf_pass'] ?? '';

    if($new_pass !== $conf_pass){
        $error = "Galti: Passwords match nahi ho rahe!";
        $step = "new_password";
    } else {
        $newhash = password_hash($new_pass, PASSWORD_BCRYPT);
        $email = $_SESSION['email'] ?? '';
        if(mysqli_query($conn, "UPDATE employees SET password='$newhash' WHERE email='$email'")){
            $success = "Password successfully update ho gaya! Ab login karein.";
            $step = "login";
            unset($_SESSION['otp'], $_SESSION['email'], $_SESSION['time']);
        } else { $error = "Database error!"; }
    }
}

$remaining_time = isset($_SESSION['time']) ? max(0, 120 - (time() - $_SESSION['time'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal | Secure Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        :root { --navy: #0f172a; --blue: #2563eb; --gradient: linear-gradient(135deg, #0f172a 0%, #2563eb 100%); --light-blue: #f1f5f9; }
        * { margin:0; padding:0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .top-nav { width: 100%; height: 70px; padding: 0 5%; background: var(--gradient); display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; z-index: 1000; }
        .logo { font-weight: 800; color: #fff; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
        .btn-home { text-decoration: none; color: #fff; font-size: 0.9rem; font-weight: 600; padding: 8px 16px; border-radius: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); transition: 0.3s; }
        .login-card { width: 100%; max-width: 1000px; display: flex; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1); margin-top: 60px; }
        .visual-side { flex: 1; background: var(--navy); background-image: url('https://www.transparenttextures.com/patterns/cubes.png'), var(--gradient); padding: 50px; color: #fff; display: flex; flex-direction: column; justify-content: center; }
        .form-side { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 18px; top: 18px; color: #94a3b8; }
        .input-group input { width: 100%; padding: 16px 16px 16px 50px; background: var(--light-blue); border: 2px solid transparent; border-radius: 14px; font-size: 1rem; transition: 0.3s; }
        .input-group input:focus { border-color: var(--blue); background: #fff; outline: none; }
        .btn-action { width: 100%; padding: 16px; background: var(--blue); color: #fff; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-action:hover { background: var(--navy); transform: translateY(-2px); }
        .bottom-bar { position: fixed; bottom: 0; width: 100%; padding: 15px; background:var(--gradient); text-align: center; font-size: 0.8rem; color:white; }
        @media (max-width: 850px) { .visual-side { display: none; } .login-card { max-width: 450px; } .form-side { padding: 40px 25px; } }
    </style>
</head>
<body>

<nav class="top-nav animate__animated animate__fadeInDown">
    <div class="logo"><i class="fas fa-university"></i> <span>EXAM INSTITUTE</span></div>
    <a href="../index.php" class="btn-home"><i class="fas fa-home"></i> Home</a>
</nav>

<div class="login-card animate__animated animate__zoomIn">
    <div class="visual-side">
        <h2 style="font-size: 2.5rem; margin-bottom: 15px;">Secure Portal.</h2>
        <p style="opacity: 0.8;">Apne dashboard ko access karein aur reports manage karein.</p>
    </div>

    <div class="form-side">
        <?php if($step == 'login'): ?>
            <h3 style="font-size: 1.8rem; margin-bottom: 5px;">Welcome Back</h3>
            <p style="color: #64748b; margin-bottom: 30px;">Login to your account</p>
            <form method="POST">
                <input type="hidden" name="step" value="login">
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="Email Address" required></div>
                <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Password" required></div>
                <button type="submit" name="emp_login" class="btn-action">Login to Portal</button>
            </form>
            <form method="POST"><input type="hidden" name="step" value="forgot"><button type="submit" style="background:none; border:none; color:var(--blue); font-weight:700; margin-top:20px; cursor:pointer; width:100%;">Forgot Password?</button></form>

        <?php elseif($step == 'forgot'): ?>
            <h3>Recovery</h3>
            <form method="POST">
                <input type="hidden" name="step" value="forgot">
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="reset_email" placeholder="Registered Email" required></div>
                <button type="submit" name="send_otp" class="btn-action">Send OTP</button>
                <button type="submit" name="step" value="login" style="background:none; border:none; width:100%; margin-top:15px; cursor:pointer; color:#94a3b8;">Back to Login</button>
            </form>

        <?php elseif($step == 'verify_otp'): ?>
            <div class="timer-ui" style="background: #fff1f2; color: #e11d48; padding: 10px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                <i class="fas fa-clock"></i> OTP valid for: <span id="timer">02:00</span>
            </div>
            <h3>Verify OTP</h3>
            <form method="POST">
                <input type="hidden" name="step" value="verify_otp">
                <div class="input-group"><input type="text" name="otp" placeholder="6-Digit Code" maxlength="6" required style="text-align:center; letter-spacing:8px; font-weight:800;"></div>
                <button type="submit" name="check_otp" class="btn-action">Verify & Continue</button>
            </form>
            <script>
                let sec = <?php echo $remaining_time; ?>;
                const clock = setInterval(() => {
                    let m = Math.floor(sec/60), s = sec%60;
                    document.getElementById('timer').innerHTML = `${m}:${s < 10 ? '0'+s : s}`;
                    if(sec <= 0) { clearInterval(clock); location.reload(); }
                    sec--;
                }, 1000);
            </script>

        <?php elseif($step == 'new_password'): ?>
            <h3>Set New Password</h3>
            <form method="POST">
                <input type="hidden" name="step" value="new_password">
                <div class="input-group"><i class="fas fa-key"></i><input type="password" name="new_pass" placeholder="New Password" minlength="6" required></div>
                <div class="input-group"><i class="fas fa-check-double"></i><input type="password" name="conf_pass" placeholder="Confirm Password" minlength="6" required></div>
                <button type="submit" name="update_pass" class="btn-action">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer class="bottom-bar">
    &copy; <?php echo date("Y"); ?> <b>EXAM INSTITUTE</b>
</footer>

<script>
    <?php if($error): ?> Swal.fire({ icon: 'error', title: 'Error', text: '<?php echo $error; ?>', confirmButtonColor: '#2563eb' }); <?php endif; ?>
    <?php if($success): ?> Swal.fire({ icon: 'success', title: 'Done!', text: '<?php echo $success; ?>', timer: 2500, showConfirmButton: false }); <?php endif; ?>
</script>

</body>
</html>