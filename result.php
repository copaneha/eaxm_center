<?php
include("config.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['student_id'])) { 
    die("<div class='container mt-5 alert alert-danger text-center'>Unauthorized Access! Please Login.</div>"); 
}

$student_id = $_SESSION['student_id'];

// 1. Official Declared Results
$query = "SELECT res.*, e.exam_name, e.exam_date FROM exam_submissions res 
          JOIN exams e ON res.exam_id = e.exam_id 
          WHERE res.student_id = '$student_id' AND res.is_issued = 1 
          ORDER BY res.issued_at DESC";
$result = mysqli_query($conn, $query);

// 2. Statistics Calculation
$stats_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM exam_submissions WHERE student_id = '$student_id'");
$total_exams = mysqli_fetch_assoc($stats_res)['total'];

// 3. Results Awaited (Pending)
$pending_query = mysqli_query($conn, "SELECT e.exam_name, res.submitted_at FROM exam_submissions res 
                 JOIN exams e ON res.exam_id = e.exam_id 
                 WHERE res.student_id = '$student_id' AND res.is_issued = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Student Dashboard | E-Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        :root {
            --primary-blue: #002366;
            --secondary-blue: #1e3c72;
            --accent-gold: #c5a059;
            --light-bg: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body { 
            background-color: var(--light-bg); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b; 
            padding-top: 80px;
        }

        /* Navbar Customization */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            position: fixed;
            top: 0; width: 100%; z-index: 1000;
        }

        /* Banner Section */
        .top-banner { 
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); 
            padding: 80px 0 160px 0; 
            color: white; 
            border-radius: 0 0 60px 60px;
            position: relative;
            overflow: hidden;
        }

        .top-banner::before {
            content: ""; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
            background: rgba(255,255,255,0.05); border-radius: 50%;
        }

        /* Stats Cards */
        .stats-wrapper { margin-top: -100px; position: relative; z-index: 5; }
        .stat-box { 
            background: white; border: none; border-radius: 24px; 
            padding: 30px; box-shadow: var(--card-shadow); transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.8);
        }
        .stat-box:hover { transform: translateY(-10px); box-shadow: 0 20px 30px rgba(0,0,0,0.1); }
        
        .icon-circle { 
            width: 60px; height: 60px; border-radius: 18px; 
            display: flex; align-items: center; justify-content: center; font-size: 24px; 
            margin-bottom: 20px;
        }

        /* Result Cards */
        .res-card { 
            background: white; border: none; border-radius: 25px; 
            box-shadow: var(--card-shadow); margin-bottom: 30px; 
            transition: 0.3s; border: 1px solid rgba(0,0,0,0.02);
        }
        .res-card:hover { border-color: var(--accent-gold); }
        
        .score-display {
            background: linear-gradient(135deg, #f0f4ff 0%, #d9e2ff 100%);
            color: var(--primary-blue);
            padding: 20px; border-radius: 20px; 
            font-weight: 800; font-size: 30px;
            min-width: 100px; text-align: center;
        }

        /* Buttons & Badges */
        .btn-download {
            background: var(--primary-blue); color: white; border-radius: 14px;
            padding: 12px 28px; font-weight: 700; transition: 0.3s; border: none;
            letter-spacing: 0.5px;
        }
        .btn-download:hover { background: var(--accent-gold); transform: scale(1.05); color: white; }

        .pending-item {
            background: #fffcf5; border: 1px solid #fef3c7;
            padding: 18px; border-radius: 20px; margin-bottom: 15px;
        }

        /* Support Section */
        .support-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 25px; padding: 30px; color: white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        /* Professional Footer */
        .footer-premium {
            background: var(--primary-blue);
            color: white;
            border-radius: 50px 50px 0 0;
           
            margin-top: 100px;
        }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="btn-back-home text-decoration-none" style="color: var(--primary-blue); font-weight: 700;">
            <i class="fas fa-arrow-left me-2"></i> Home Dashboard
        </a>
        <div class="d-none d-md-block">
            <span class="badge bg-light text-primary p-2 px-3 rounded-pill border">
                <i class="fas fa-check-circle me-1"></i> Session Active: Student Verified
            </span>
        </div>
    </div>
</nav>

<div class="top-banner text-center">
    <div class="container">
        <h1 class="fw-800 display-5 mb-3">Academic Repository</h1>
        <p class="opacity-75 lead fw-light">Access your verified digital certificates and exam transcripts</p>
    </div>
</div>

<div class="container stats-wrapper">
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-4">
            <div class="stat-box">
                <div class="icon-circle bg-primary bg-opacity-10 text-primary"><i class="fas fa-layer-group"></i></div>
                <h6 class="text-muted fw-bold small text-uppercase mb-1">Total Exams</h6>
                <span class="h2 fw-800"><?php echo $total_exams; ?></span>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-box">
                <div class="icon-circle bg-success bg-opacity-10 text-success"><i class="fas fa-clipboard-check"></i></div>
                <h6 class="text-muted fw-bold small text-uppercase mb-1">Results Issued</h6>
                <span class="h2 fw-800"><?php echo mysqli_num_rows($result); ?></span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-box">
                <div class="icon-circle bg-warning bg-opacity-10 text-warning"><i class="fas fa-spinner fa-spin"></i></div>
                <h6 class="text-muted fw-bold small text-uppercase mb-1">Results Awaited</h6>
                <span class="h2 fw-800"><?php echo mysqli_num_rows($pending_query); ?></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <span class="bg-primary p-1 rounded me-3" style="width: 6px; height: 24px;"></span>
                OFFICIAL TRANSCRIPTS
            </h5>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="res-card">
                    <div class="res-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="fw-800 mb-2" style="color: var(--primary-blue);"><?php echo htmlspecialchars($row['exam_name']); ?></h3>
                                <p class="text-muted mb-0 small">
                                    <i class="far fa-calendar-check me-2"></i>Declared: <?php echo date('d M, Y', strtotime($row['issued_at'])); ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="score-display d-inline-block">
                                    <?php echo $row['score']; ?><small class="fs-6">%</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="progress my-4" style="height: 12px; border-radius: 50px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $row['score']; ?>%; border-radius: 50px;"></div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                                <i class="fas fa-fingerprint text-success me-1"></i> Digital Signature Verified
                            </span>
                            <a href="admin/generate_result.php?submission_id=<?php echo $row['id']; ?>" class="btn btn-download shadow-sm">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/6108/6108312.png" width="80" class="mb-3 opacity-25">
                    <p class="text-muted">No academic transcripts found in our digital vault.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <h5 class="fw-bold mb-4">NOTIFICATIONS</h5>
            
            <div class="stat-box mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Awaiting Review</h6>
                <?php if(mysqli_num_rows($pending_query) > 0): ?>
                    <?php while($p = mysqli_fetch_assoc($pending_query)): ?>
                        <div class="pending-item">
                            <h6 class="fw-bold mb-1 small"><?php echo $p['exam_name']; ?></h6>
                            <div class="d-flex align-items-center text-warning small fw-bold">
                                <span class="spinner-grow spinner-grow-sm me-2"></span> Evaluation in progress
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle text-success mb-2 d-block fs-3"></i>
                        <p class="text-muted small mb-0">No pending results</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="support-card shadow-lg">
                <h5 class="fw-bold text-warning mb-3"><i class="fas fa-headset me-2"></i>Help Desk</h5>
                <p class="small opacity-75 mb-4">Agar aapke marks ya certificate mein koi discrepancy hai, toh turant Controller of Exams ko report karein.</p>
                <a href="mailto:support@examportal.gov" class="btn btn-warning w-100 fw-bold py-2 rounded-3">
                    <i class="fas fa-paper-plane me-2"></i>Raise a Ticket
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="footer-premium text-center">
    <div class="container">
        <div class="mb-4">
            <i class="fas fa-shield-alt text-warning fa-3x mb-3"></i>
            <h4 class="fw-bold">Authorized Digital Dashboard</h4>
            <p class="text-white-50 small">Official Exam Management System v4.0.2</p>
        </div>
        <hr class="opacity-25 my-4">
        <div class="row align-items-center py-3">
            <div class="col-md-6 text-md-start">
                <p class="small text-white-50 mb-0">
                    &copy; 2026 E-Portal Management | Strictly Confidential Academic Records.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="small text-white-50">
                    <span class="me-3">Privacy Policy</span>
                    <span>Terms of Service</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>