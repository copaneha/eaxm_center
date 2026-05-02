<?php
include("config.php");
include("admin/mail_config.php"); // PHPMailer config file include ki gayi hai
$status_msg = "";
// --- Database se course nikalne ka logic (Dropdown ke liye) ---
$course_options = "";
$course_query = "SELECT course_name FROM courses ORDER BY course_name ASC";
$course_result = mysqli_query($conn, $course_query);

if ($course_result && mysqli_num_rows($course_result) > 0) {
    while ($row = mysqli_fetch_assoc($course_result)) {
        $c_name = $row['course_name'];
        $course_options .= "<option value='$c_name'>$c_name</option>";
    }
} else {
    $course_options = "<option disabled>No courses available</option>";
}

if (isset($_POST['register'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $f_name  = mysqli_real_escape_string($conn, $_POST['father_name'] ?? '');
    $email   = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone   = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $gender  = mysqli_real_escape_string($conn, $_POST['gender'] ?? ''); 
    $dob     = mysqli_real_escape_string($conn, $_POST['dob'] ?? '');     
    $course  = mysqli_real_escape_string($conn, $_POST['course'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? ''); 

    // Image Upload Logic
    $target_dir = "image/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo_name);
    } else { $photo_name = "default.png"; }

    // Database Insertion
    $sql = "INSERT INTO students (name, father_name, email, phone, gender, dob, course, address, photo, status) 
            VALUES ('$name', '$f_name', '$email', '$phone', '$gender', '$dob', '$course', '$address', '$photo_name', 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        $status_msg = "success";

        // --- EMAIL SENDING PROCESS ---
        try {
            $mail->addAddress($email, $name); 
            $mail->isHTML(true);
            $mail->Subject = 'Admission Successfully Registered - Academic Portal';
            
            // Professional HTML Email Body
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; max-width: 600px; margin: auto;'>
                    <div style='background: #1e3a8a; color: #ffffff; padding: 25px; text-align: center;'>
                        <h1 style='margin:0; font-size: 24px;'>Academic Portal Core</h1>
                        <p style='margin:5px 0 0 0; opacity: 0.8;'>Registration Confirmation</p>
                    </div>
                    <div style='padding: 30px; color: #334155; line-height: 1.6;'>
                        <p style='font-size: 18px;'>Hello <strong>$name</strong>,</p>
                        <p>We are pleased to inform you that your registration for <strong>$course</strong> has been successfully received by our system.</p>
                        
                        <div style='background: #f1f5f9; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='margin-top: 0; color: #1e3a8a;'>Application Summary:</h3>
                            <table style='width: 100%; font-size: 14px;'>
                                <tr><td><strong>Name:</strong></td><td>$name</td></tr>
                                <tr><td><strong>Father's Name:</strong></td><td>$f_name</td></tr>
                                <tr><td><strong>Course:</strong></td><td>$course</td></tr>
                                <tr><td><strong>Status:</strong></td><td><span style='color: #f59e0b;'>Pending Verification</span></td></tr>
                            </table>
                        </div>
                        
                        <p>Our administration will review your details shortly. Please keep this email for your future reference.</p>
                        <p style='margin-bottom: 0;'>Regards,</p>
                        <p style='margin-top: 0; font-weight: bold; color: #1e3a8a;'>The Admissions Team</p>
                    </div>
                    <div style='background: #f8fafc; padding: 15px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;'>
                        This is an automated system-generated notification. Please do not reply to this email.
                    </div>
                </div>";

            $mail->send();
        } catch (Exception $e) {
            // Error handling agar mail na jaye (log kar sakte hain)
        }
    } else { 
        $status_msg = "error"; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | Academic Portal Core</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #1e3a8a; 
            --accent: #3b82f6; 
            --bg-body: #f0f4f8; 
            --card-bg: #ffffff;
            --input-bg: #f8fafc;
            --sidebar-grad: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            --text-dark: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0, transparent 40%), 
                radial-gradient(at 100% 100%, rgba(30, 58, 138, 0.05) 0, transparent 40%);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .navbar {
            background: var(--primary);
            border-bottom: 3px solid var(--accent);
            padding: 0.8rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand { font-weight: 800; color: #ffffff !important; letter-spacing: -1px; }

        .main-wrapper { padding: 50px 0; }

        .form-container {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        .info-panel {
            background: var(--sidebar-grad);
            padding: 45px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .info-panel h3 { font-weight: 800; margin-bottom: 20px; }

        .feature-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-icon {
            width: 35px; height: 35px; background: white; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--primary); margin-right: 12px;
        }

        .form-header { padding: 40px 50px 20px 50px; }
        .form-header h1 { font-weight: 800; color: var(--primary); }
        .form-body { padding: 0 50px 45px 50px; }

        .section-label {
            display: block; font-size: 0.75rem; font-weight: 800; color: var(--accent);
            text-transform: uppercase; letter-spacing: 1.5px; margin: 35px 0 20px 0;
            padding: 8px 15px; background: rgba(59, 130, 246, 0.08);
            border-left: 4px solid var(--accent); border-radius: 0 8px 8px 0;
        }

        .form-label { font-weight: 700; font-size: 0.85rem; color: #475569; margin-bottom: 8px; }

        .form-control, .form-select {
            background-color: var(--input-bg); border: 2px solid #e2e8f0;
            padding: 12px 16px; border-radius: 12px; font-weight: 500; transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff; border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); outline: none;
        }

        .btn-register {
            background: var(--sidebar-grad); color: #ffffff; border: none;
            padding: 16px; font-weight: 800; border-radius: 14px; width: 100%;
            margin-top: 20px; transition: 0.3s; box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
        }

        .btn-register:hover { transform: translateY(-3px); filter: brightness(1.1); }

        @media (max-width: 991px) {
            .form-header, .form-body { padding: 30px; }
            .info-panel { border-radius: 20px 20px 0 0; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="fas fa-university"></i>
            <span>ACADEMIC<span style="font-weight:400; color:rgba(255,255,255,0.7)">PORTAL</span></span>
        </a>
        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold">
            <i class="fas fa-sign-out-alt me-2"></i>Exit
        </a>
    </div>
</nav>

<div class="main-wrapper">
    <div class="container">
        <div class="row g-0 form-container">
            <div class="col-lg-4 info-panel">
                <div>
                    <h3>Registration 2026</h3>
                    <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; line-height: 1.6;">Your journey to excellence starts here. Register now to join our academic community.</p>
                    
                    <div class="mt-5">
                        <div class="feature-box d-flex align-items-center">
                            <div class="feature-icon"><i class="fas fa-envelope-open-text"></i></div>
                            <div class="small fw-bold">Instant Email Confirmation</div>
                        </div>
                        <div class="feature-box d-flex align-items-center">
                            <div class="feature-icon"><i class="fas fa-file-invoice"></i></div>
                            <div class="small fw-bold">Digital Record Management</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="badge bg-warning text-dark p-2 w-100 mb-2" style="border-radius: 10px;">
                        <i class="fas fa-shield-alt me-2"></i> Verified Enrollment Portal
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="form-header">
                    <h1>Student Registration</h1>
                    <p class="text-muted small">Enter your official details to begin the admission process.</p>
                </div>

                <div class="form-body">
                    <form method="POST" enctype="multipart/form-data">
                        <span class="section-label">Identity Details</span>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Student's name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name</label>
                                <input type="text" name="father_name" class="form-control" placeholder="Guardian's name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" selected disabled>Select Gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                        </div>

                        <span class="section-label">Contact & Course</span>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="for notifications" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91" required>
                            </div>
                           <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <select name="course" class="form-select" required>
                                    <option value="" selected disabled>Select Course</option>
                                    <?php echo $course_options; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="photo" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-register">
                            Confirm Registration & Send Email
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if($status_msg == "success"): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registration Done',
            text: 'Confirmation email has been sent to student.',
            confirmButtonColor: '#1e3a8a'
        }).then(() => { window.location.href = 'index.php'; });
    <?php elseif($status_msg == "error"): ?>
        Swal.fire({ icon: 'error', title: 'Error', text: 'Database connection failed.' });
    <?php endif; ?>
</script>

</body>
</html>

