<?php
include "config.php";

// Session start karein taaki success message ko redirect ke baad dikha sakein


if (isset($_POST['submit_request'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['issue_type']); 
    $message = mysqli_real_escape_string($conn, $_POST['description']);

    // Table name aur columns check karein (Aapki table support_tickets hai)
    $query = "INSERT INTO contact_messages(name, email, subject, message, status) 
              VALUES ('$name', '$email', '$subject', '$message', 'Pending')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['status'] = "success";
        // Wapas isi page par redirect karein taaki POST data clear ho jaye
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['status'] = "error";
        $_SESSION['error_log'] = mysqli_error($conn);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Redirect ke baad session se status nikalna
$status = isset($_SESSION['status']) ? $_SESSION['status'] : "";
$error_detail = isset($_SESSION['error_log']) ? $_SESSION['error_log'] : "";

// Ek baar status mil jaye toh session clear kar dein taaki refresh par alert na aaye
unset($_SESSION['status']);
unset($_SESSION['error_log']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - Premium Exam Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --gradient-1: #6366f1;
            --gradient-2: #a855f7;
            --gradient-3: #ec4899;
            --primary-dark: #0f172a;
            --glass-bg: rgba(255, 255, 255, 0.98);
            --text-dark: #1e293b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; padding: 0; min-height: 100vh;
            display: flex; flex-direction: column;
            background-color: #f9faff;
            background-image: 
                radial-gradient(at 0% 0%, rgba(213, 213, 224, 0.91) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgb(248, 240, 244) 0, transparent 50%);
            overflow-x: hidden;
        }

        .top-navbar {
            height: 75px;
            background: linear-gradient(135deg, rgba(5, 5, 16, 0.9), rgba(95, 23, 211, 0.9), rgba(50, 21, 215, 0.9));
            backdrop-filter: blur(15px);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px; position: fixed; top: 0; left: 0; right: 0;
            z-index: 1000; border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .back-btn {
            color: white; text-decoration: none; font-weight: 700;
            display: flex; align-items: center; gap: 10px; padding: 10px 18px;
            border-radius: 50px; background: rgba(255, 255, 255, 0.15);
            transition: 0.4s; border: 1px solid rgba(255, 255, 255, 0.2); font-size: 14px;
        }

        .back-btn:hover { background: white; color: var(--gradient-1); transform: scale(1.05); }
        .nav-text { color: white; font-size: 20px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; }

        .main-wrapper { flex: 1; display: flex; justify-content: center; align-items: center; padding: 120px 20px 100px; }

        .contact-container {
            display: grid; grid-template-columns: 1fr 1.6fr; max-width: 1100px; width: 100%;
            background: var(--glass-bg); border-radius: 40px; overflow: hidden;
            box-shadow: 0 50px 100px rgba(0,0,0,0.15); border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-info {
            background: linear-gradient(180deg, var(--gradient-1), var(--gradient-2));
            padding: 60px; color: white; display: flex; flex-direction: column; justify-content: space-between;
        }

        .info-header h2 { font-size: 36px; font-weight: 800; margin: 0 0 20px 0; }
        .info-header p { font-size: 16px; opacity: 0.9; line-height: 1.6; }

        .info-item { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; font-weight: 500; }
        .info-item i { 
            font-size: 18px; background: rgba(255,255,255,0.2); 
            width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 15px; 
        }

        .contact-form-area { padding: 60px; color: var(--text-dark); }
        .form-group { margin-bottom: 25px; }
        .form-group label { font-size: 14px; font-weight: 800; color: #475569; margin-bottom: 10px; display: block; }

        .input-box { position: relative; }
        .input-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px; }

        input, textarea {
            width: 100%; padding: 16px 20px 16px 50px; border: 2px solid #e2e8f0;
            border-radius: 16px; font-family: inherit; font-size: 16px;
            background: #f1f5f9; transition: 0.3s; box-sizing: border-box;
        }

        textarea { padding-left: 20px; resize: none; }
        input:focus, textarea:focus { border-color: var(--gradient-1); background: white; outline: none; box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1); }

        .btn-send {
            width: 100%; padding: 18px; background: linear-gradient(90deg, var(--gradient-1), var(--gradient-2));
            color: white; border: none; border-radius: 16px; font-size: 18px; font-weight: 800;
            cursor: pointer; transition: 0.4s; display: flex; justify-content: center; align-items: center;
            gap: 12px; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-send:hover { transform: translateY(-5px); filter: brightness(1.1); }

        .bottom-bar {
            background: linear-gradient(135deg, rgba(7, 7, 8, 0.9), rgba(18, 70, 211, 0.9), rgba(55, 19, 139, 0.9));
            padding: 30px; text-align: center; font-size: 15px; font-weight: 500; color: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 950px) {
            .contact-container { grid-template-columns: 1fr; }
            .contact-info { display: none; }
            .nav-text { font-size: 14px; }
        }
    </style>
</head>
<body>

<nav class="top-navbar">
    <a href="javascript:history.back()" class="back-btn">
        <i class="fa fa-arrow-left"></i> <span>GO BACK</span>
    </a>
    <div class="nav-text">Exam Support Portal</div>
    <div style="width: 100px;"></div>
</nav>

<div class="main-wrapper">
    <div class="contact-container">
        <div class="contact-info">
            <div class="info-header">
                <h2>Ready to Help!</h2>
                <p>Facing issues with your exam submission or login? Our technical experts are available 24/7 to ensure your success.</p>
            </div>
            <div class="info-details">
                <div class="info-item"><i class="fa fa-envelope-open"></i><span>support@exam-portal.com</span></div>
                <div class="info-item"><i class="fa fa-headset"></i><span>Toll Free: 1800-123-456</span></div>
            </div>
            <div style="display: flex; gap: 20px; font-size: 20px;">
                <i class="fab fa-facebook-f"></i><i class="fab fa-instagram"></i><i class="fab fa-whatsapp"></i>
            </div>
        </div>

        <div class="contact-form-area">
            <form id="contactForm" method="POST" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-box">
                        <i class="fa fa-user-tie"></i>
                        <input type="text" name="full_name" placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Registration Email</label>
                    <div class="input-box">
                        <i class="fa fa-at"></i>
                        <input type="email" name="email" placeholder="student@portal.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Issue Type</label>
                    <div class="input-box">
                        <i class="fa fa-bug"></i>
                        <input type="text" name="issue_type" placeholder="e.g. Technical Glitch, Login Issue" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brief Description</label>
                    <textarea name="description" rows="4" placeholder="Explain your issue in detail..." required></textarea>
                </div>

                <button type="submit" name="submit_request" class="btn-send">
                    SUBMIT REQUEST <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<footer class="bottom-bar">
    &copy; 2026 Online Exam Center Management System | Professional Support
</footer>

<script>
    <?php if($status == "success"): ?>
        Swal.fire({
            icon: 'success',
            title: 'Ticket Raised!',
            text: 'Your request has been saved successfully.',
            confirmButtonColor: '#6366f1'
        });
    <?php elseif($status == "error"): ?>
        Swal.fire({
            icon: 'error',
            title: 'Action Failed!',
            text: 'Error: <?php echo $error_detail; ?>',
        });
    <?php endif; ?>
</script>

</body>
</html>