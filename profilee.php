<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
include "config.php"; 

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : $_SESSION['student_id'];
if ($student_id != $_SESSION['student_id']) { $student_id = $_SESSION['student_id']; }

$query = "SELECT * FROM students WHERE student_id = '$student_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if($result && mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    header("Location: dashboard.php");
    exit();
}

$photo_path = "image/" . ($student['photo'] ?? ''); 
if (empty($student['photo']) || !file_exists($photo_path)) {
    $photo_path = "https://ui-avatars.com/api/?name=" . urlencode($student['name']) . "&background=6366f1&color=fff&size=250";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile | Smart CBT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --dark-bg: #0f172a;
            --accent-blue: #3b82f6;
            --card-glass: rgba(255, 255, 255, 0.9);
        }

        body { 
            background: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #334155;
            min-height: 100vh;
            padding-top: 90px;
        }

        /* --- Floating Header --- */
        .navbar-custom {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.4rem;
            color: #fff !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- Main Profile Card --- */
        .main-profile-wrapper {
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
        }

        /* Sidebar Color & Animation */
        .sidebar-panel {
            background: #f8fafc;
            padding: 50px 35px;
            border-right: 1px solid #f1f5f9;
            position: relative;
            z-index: 1;
        }

        .sidebar-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 5px;
            background: var(--primary-gradient);
        }

        .profile-img-box {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }

        .profile-img-box img {
            width: 160px; height: 160px;
            border-radius: 40px;
            object-fit: cover;
            border: 6px solid #fff;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .profile-img-box:hover img { transform: rotate(-3deg) scale(1.05); }

        /* --- Info Cards & Labels --- */
        .detail-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 25px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .detail-card:hover {
            transform: translateY(-8px);
            border-color: #6366f1;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.05);
        }

        .icon-circle {
            width: 45px; height: 45px;
            background: #eff6ff;
            color: #6366f1;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .label-text {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94a3b8;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .value-text {
            font-weight: 700;
            color: var(--dark-bg);
            font-size: 1.05rem;
        }

        /* --- Status Badges --- */
        .badge-verified {
            background: #ecfdf5;
            color: #10b981;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #d1fae5;
        }

        /* --- Professional Buttons --- */
        .btn-action {
            padding: 12px 25px;
            border-radius: 16px;
            font-weight: 700;
            transition: 0.4s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-print {
            background: var(--dark-bg);
            color: #fff;
            border: none;
        }

        .btn-print:hover {
            background: #1e293b;
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
        }

        @media (max-width: 991px) {
            .sidebar-panel { border-right: none; border-bottom: 1px solid #f1f5f9; }
            body { padding-top: 80px; }
        }

        /* Print Optimization */
        @media print {
            .navbar-custom, .btn-action, .no-print { display: none !important; }
            body { padding-top: 0; background: #fff; }
            .main-profile-wrapper { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>

<nav class="navbar-custom no-print">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="#" class="brand-logo">
            <i class="fas fa-shield-halved"></i>
            <span>SMART<span class="fw-300">CBT</span></span>
        </a>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-link text-white text-decoration-none fw-600 small">Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">Logout</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <div class="main-profile-wrapper" data-aos="zoom-in-up">
                <div class="row g-0">
                    
                    <div class="col-lg-4 sidebar-panel text-center">
                        <div class="profile-img-box">
                            <img src="<?php echo $photo_path; ?>" alt="Student">
                        </div>
                        
                        <h3 class="fw-800 text-dark mb-1"><?php echo strtoupper($student['name']); ?></h3>
                        <p class="text-muted fw-600 mb-4">ID: <?php echo $student['student_id']; ?></p>

                        <div class="d-flex justify-content-center mb-4">
                            <?php if($student['status'] == 'rejected'): ?>
                                <span class="badge bg-light text-danger border border-danger fw-bold rounded-pill px-3">Access Denied</span>
                            <?php else: ?>
                                <span class="badge-verified"><i class="fas fa-check-circle me-1"></i> Verified Candidate</span>
                            <?php endif; ?>
                        </div>

                        <div class="action-btns no-print mt-5 d-grid gap-3">
                            <button onclick="window.print()" class="btn-action btn-print">
                                <i class="fas fa-print"></i> Download Profile
                            </button>
                            <a href="dashboard.php" class="btn-action btn-outline-secondary border-2">
                                <i class="fas fa-arrow-left"></i> Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-8 p-4 p-md-5 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <div>
                                <h4 class="fw-800 mb-0">Identity Profile</h4>
                                <div style="width: 50px; height: 4px; background: var(--primary-gradient); border-radius: 10px; margin-top: 8px;"></div>
                            </div>
                            <div class="text-end no-print">
                                <span class="badge bg-soft-primary text-primary fw-bold px-3 py-2 rounded-pill" style="background: #eef2ff;">Session 2026</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                                <div class="detail-card">
                                    <div class="icon-circle"><i class="fas fa-user-friends"></i></div>
                                    <div class="label-text">Father / Guardian</div>
                                    <div class="value-text"><?php echo $student['father_name'] ?? 'N/A'; ?></div>
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                                <div class="detail-card">
                                    <div class="icon-circle"><i class="fas fa-envelope-open-text"></i></div>
                                    <div class="label-text">Email Address</div>
                                    <div class="value-text text-primary small"><?php echo $student['email']; ?></div>
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                                <div class="detail-card" style="border-left: 5px solid #6366f1;">
                                    <div class="icon-circle" style="background: #f5f3ff; color: #8b5cf6;"><i class="fas fa-book"></i></div>
                                    <div class="label-text">Allocated Course</div>
                                    <div class="value-text text-uppercase"><?php echo $student['course']; ?></div>
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                <div class="detail-card">
                                    <div class="icon-circle"><i class="fas fa-phone-alt"></i></div>
                                    <div class="label-text">Emergency Contact</div>
                                    <div class="value-text"><?php echo $student['phone']; ?></div>
                                </div>
                            </div>

                            <div class="col-12" data-aos="fade-up" data-aos-delay="500">
                                <div class="detail-card bg-light border-0">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="label-text">Mailing Address</div>
                                            <div class="value-text small fw-500 text-muted"><?php echo $student['address'] ?? 'Official address not provided.'; ?></div>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                            <div class="label-text">Roll Number</div>
                                            <div class="h5 fw-800 mb-0"><?php echo $student['roll_no'] ?? 'Pending'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 d-flex align-items-center gap-3 p-3 rounded-4" style="background: #fffbeb; border: 1px solid #fef3c7;">
                            <i class="fas fa-info-circle text-warning fs-4"></i>
                            <p class="mb-0 small text-dark fw-500">
                                <strong>System Note:</strong> For security reasons, please do not share your QR login code with anyone.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center mt-5 no-print">
                <p class="text-muted small fw-600">
                    &copy; 2026 Online Examination Authority <br>
                    <span class="text-primary">Secured by SmartCBT Infrastructure</span>
                </p>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ 
        duration: 1000, 
        once: true,
        easing: 'ease-out-back'
    });
</script>
</body>
</html>