<?php
session_start();
include "config.php"; 
if (file_exists("admin/mail_config.php")) { include "admin/mail_config.php"; }

$error = "";
$success = "";
$step = $_POST['step'] ?? 'login';

// --- 1. STUDENT LOGIN LOGIC ---
if (isset($_POST['login'])) {
    $roll_no = mysqli_real_escape_string($conn, $_POST['roll_no']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $pass    = $_POST['password'];

    $query = "SELECT * FROM students WHERE roll_no = '$roll_no' AND email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($pass, $row['password']) || $pass == $row['password']){
            $_SESSION['student_id']   = $row['student_id'];
            $_SESSION['student_name'] = $row['name'];
            $_SESSION['course']       = $row['course'];
            $_SESSION['photo']        = $row['photo'];
            $login_success_name = $row['name'];
            echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 2000);</script>";
        } else { $error = "Invalid Password!"; }
    } else { $error = "Roll Number aur Email ka combination galat hai!"; }
}

// --- 2. SEND OTP LOGIC ---
if(isset($_POST['send_otp'])){
    $email = mysqli_real_escape_string($conn, $_POST['reset_email']);
    $res = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");

    if(mysqli_num_rows($res) > 0){
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_student_email'] = $email;
        $_SESSION['otp_expiry'] = time() + 120; 

        if(isset($mail)){
            try {
                $mail->clearAddresses();
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Password Reset OTP";
                $mail->Body = "Aapka Password Reset OTP hai: <b>$otp</b>. Yeh sirf 2 minute tak valid hai.";
                if($mail->send()){
                    $success = "OTP bhej diya gaya hai!";
                    $step = "verify_otp";
                }
            } catch (Exception $e) { $error = "Mail error!"; }
        } else { $error = "Mail settings missing!"; }
    } else { $error = "Email registered nahi hai!"; }
}

// --- 3. VERIFY OTP ---
if(isset($_POST['check_otp'])){
    if(time() > $_SESSION['otp_expiry']){ 
        $error = "OTP Expire ho gaya!"; 
        $step = "forgot";
    } elseif($_POST['otp'] == $_SESSION['reset_otp']){
        $step = "new_password";
    } else { $error = "Galat OTP!"; $step = "verify_otp"; }
}

// --- 4. UPDATE PASSWORD ---
if(isset($_POST['update_pass'])){
    $pass1 = $_POST['new_pass'];
    $pass2 = $_POST['conf_pass'];

    if($pass1 !== $pass2){
        $error = "Passwords match nahi ho rahe!";
        $step = "new_password";
    } else {
        $newhash = password_hash($pass1, PASSWORD_BCRYPT);
        $email = $_SESSION['reset_student_email'];
        if(mysqli_query($conn, "UPDATE students SET password='$newhash' WHERE email='$email'")){
            $success = "Password successfully badal gaya!";
            $step = "login";
            unset($_SESSION['reset_otp'], $_SESSION['otp_expiry'], $_SESSION['reset_student_email']);
        } else { $error = "Error updating password!"; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Login | Secure Exam Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --govt-blue: #003366; --accent-orange: #f39c12; }
        
        body { 
            background-color: #f4f7f9; 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
        }

        .top-bar { 
            background: var(--govt-blue); 
            color: white; 
            padding: 10px 5%; 
            font-size: 0.85rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .exit-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-weight: 600;
            transition: 0.3s;
            font-size: 0.75rem;
        }

        .exit-btn:hover {
            background: #d9534f;
            border-color: #d9534f;
            color: white;
        }

        .main-header { 
            background: white; 
            padding: 15px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.08); 
            border-bottom: 3px solid var(--accent-orange); 
        }

        .login-card { 
            background: white; 
            border-radius: 4px; 
            border: 1px solid #dee2e6; 
            width: 100%; 
            max-width: 420px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            margin: auto; 
            overflow: hidden; 
        }

        .card-header { 
            background: #f8f9fa; 
            border-bottom: 1px solid #dee2e6; 
            padding: 20px; 
            text-align: center; 
            color: var(--govt-blue); 
            font-weight: 700; 
        }

        .card-body { padding: 30px; }

        .btn-login { 
            background: var(--govt-blue); 
            color: white; 
            width: 100%; 
            padding: 12px; 
            font-weight: 600; 
            text-transform: uppercase; 
            border: none; 
            transition: 0.3s; 
        }

        .btn-login:hover { background: #002244; }

        .sys-footer { 
            background: var(--govt-blue);  
            color: white; 
            padding: 12px 5%; 
            font-size: 0.85rem; 
            text-align: center; 
            border-top: 3px solid var(--accent-orange);
            width: 100%;
        }

        #timer { font-weight: bold; color: #d9534f; }
    </style>
</head>
<body>

<div class="top-bar">
    <div>
        <span class="me-3"><i class="fas fa-landmark me-2"></i>Ministry of Education</span>
        <span class="d-none d-sm-inline">Digital Exam Portal 2026</span>
    </div>
    <a href="index.php" class="exit-btn">
        <i class="fas fa-times-circle me-1"></i> EXIT
    </a>
</div>

<header class="main-header">
    <div class="d-flex align-items-center gap-3">
        <div style="width: 50px; height: 50px; background: var(--govt-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;"><i class="fas fa-university"></i></div>
        <div>
            <h1 style="font-size: 1.25rem; font-weight: 700; color: var(--govt-blue); margin: 0;">NATIONAL EXAMINATION COUNCIL</h1>
            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Digital Assessment System</p>
        </div>
    </div>
</header>

<main class="flex-grow-1 d-flex align-items-center">
    <div class="login-card my-5">
        <div class="card-header"><?php echo ($step == 'login') ? 'CANDIDATE LOGIN' : strtoupper(str_replace('_', ' ', $step)); ?></div>
        <div class="card-body">
            
            <?php if($step == 'login'): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">ROLL NUMBER</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                        <input type="text" name="roll_no" class="form-control" placeholder="Roll No." required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">EMAIL ADDRESS</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Registered Email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-login">Sign In <i class="fas fa-chevron-right ms-2"></i></button>
            </form>
            <div class="text-center mt-3">
                <form method="POST"><input type="hidden" name="step" value="forgot"><button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">Forgot Password?</button></form>
            </div>

            <?php elseif($step == 'forgot'): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">REGISTERED EMAIL</label>
                    <input type="email" name="reset_email" class="form-control" placeholder="example@mail.com" required>
                </div>
                <button type="submit" name="send_otp" class="btn btn-login">Send OTP</button>
                <button type="submit" name="step" value="login" class="btn btn-link btn-sm w-100 mt-2">Back to Login</button>
            </form>

            <?php elseif($step == 'verify_otp'): ?>
            <form method="POST">
                <div class="text-center mb-3">
                    <p class="small text-muted">OTP expires in: <span id="timer">02:00</span></p>
                    <label class="form-label small fw-bold">ENTER 6-DIGIT OTP</label>
                    <input type="text" name="otp" class="form-control text-center fw-bold fs-4" maxlength="6" required>
                </div>
                <button type="submit" name="check_otp" class="btn btn-login">Verify OTP</button>
            </form>
            <script>
                let timeLeft = 120;
                const timerDisplay = document.getElementById('timer');
                setInterval(() => {
                    if(timeLeft <= 0) {
                        timerDisplay.innerHTML = "EXPIRED";
                    } else {
                        timeLeft--;
                        let mins = Math.floor(timeLeft / 60);
                        let secs = timeLeft % 60;
                        timerDisplay.innerHTML = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }
                }, 1000);
            </script>

            <?php elseif($step == 'new_password'): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">NEW PASSWORD</label>
                    <input type="password" name="new_pass" class="form-control" minlength="6" placeholder="Min 6 characters" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">CONFIRM NEW PASSWORD</label>
                    <input type="password" name="conf_pass" class="form-control" minlength="6" placeholder="Repeat password" required>
                </div>
                <button type="submit" name="update_pass" class="btn btn-login">Update Password</button>
            </form>
            <?php endif; ?>

        </div>
    </div>
</main>

<footer class="sys-footer">
    <div class="container">
        <span><i class="fas fa-copyright me-2"></i>2026 OEMS Portal | Server IP: <strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong></span>
    </div>
</footer>

<script>
    <?php if(isset($login_success_name)): ?>
        Swal.fire({ title: 'Authenticated!', text: 'Welcome <?php echo $login_success_name; ?>', icon: 'success', showConfirmButton: false, timer: 2000 });
    <?php endif; ?>
    <?php if($error): ?>
        Swal.fire({ title: 'Error', text: '<?php echo $error; ?>', icon: 'error', confirmButtonColor: '#d33' });
    <?php endif; ?>
    <?php if($success): ?>
        Swal.fire({ title: 'Success', text: '<?php echo $success; ?>', icon: 'success', confirmButtonColor: '#003366' });
    <?php endif; ?>
</script>
</body>
</html>