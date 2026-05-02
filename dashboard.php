<?php
// 1. Timezone and Session
date_default_timezone_set("Asia/Kolkata");
session_start();
include "config.php"; 

// 2. Login Check
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// --- ERROR FIX START ---
// Make sure the column name matches your DB (is it 'id' or 'student_id'?)
$query = "SELECT * FROM students WHERE student_id = '$student_id'"; 
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // Fallback if ID column name is just 'id'
    $query = "SELECT * FROM students WHERE id = '$student_id'";
    $result = mysqli_query($conn, $query);
}

if($result && mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    session_destroy();
    header("Location: index.php?error=UserNotFound");
    exit();
}
// --- ERROR FIX END ---

$current_exam_id = 1; 

// Photo path logic
$photo_path = "image/" . ($student['photo'] ?? ''); 
if (empty($student['photo']) || !file_exists($photo_path)) {
    $photo_path = "https://ui-avatars.com/api/?name=" . urlencode($student['name'] ?? 'Student') . "&background=003366&color=fff&size=150";
}

$admit_card_url = "admit_card.php?student_id=" . $student_id . "&exam_id=" . $current_exam_id;
$current_date = date("Y-m-d");
$current_time = date("H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Online Examination System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { 
            --primary-dark: #003366; 
            --accent-orange: #f39c12; 
            --bg-body: #f0f2f5; 
            --sidebar-width: 280px; 
        }
        
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; overflow-x: hidden; }

        /* --- Professional Sidebar --- */
        .sidebar { 
            height: 100vh; width: var(--sidebar-width); 
            position: fixed; background: white; 
            border-right: 1px solid #ddd; transition: 0.3s; z-index: 1000; 
        }
        .sidebar-header { 
            padding: 20px; background: var(--primary-dark); color: white; 
            text-align: center; border-bottom: 4px solid var(--accent-orange);
        }
        .nav-link { 
            color: #444; padding: 15px 25px; 
            display: flex; align-items: center; 
            font-weight: 500; border-bottom: 1px solid #f0f0f0;
        }
        .nav-link i { width: 30px; color: var(--primary-dark); }
        .nav-link:hover, .nav-link.active { 
            background: #f8f9fa; color: var(--primary-dark); 
            border-left: 5px solid var(--primary-dark); 
        }
        .nav-link.text-danger:hover { background: #fff5f5; border-left-color: #dc3545; }

        /* --- Main Content --- */
        .main-content { margin-left: var(--sidebar-width); transition: 0.3s; }
        
        .top-nav {
            background: white; padding: 12px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
        }

        .content-body { padding: 30px; }

        /* --- Cards --- */
        .card-custom {
            background: white; border: 1px solid #dee2e6;
            border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 25px;
        }
        .card-header-custom {
            padding: 15px 20px; border-bottom: 1px solid #dee2e6;
            background: #fdfdfd; font-weight: 700; color: var(--primary-dark);
        }

        .exam-status-bar {
            background: #fff3cd; border-left: 5px solid var(--accent-orange);
            padding: 20px; border-radius: 8px; margin-bottom: 25px;
        }

        .student-profile-box {
            text-align: center; padding: 30px;
        }
        .profile-img-lg {
            width: 120px; height: 120px; border-radius: 50%;
            border: 3px solid var(--primary-dark); padding: 3px;
            object-fit: cover; margin-bottom: 15px;
        }

        .action-btn {
            background: #f8f9fa; border: 1px solid #ddd;
            padding: 20px; border-radius: 10px; text-decoration: none;
            color: var(--primary-dark); display: block; transition: 0.2s;
        }
        .action-btn:hover { background: var(--primary-dark); color: white !important; transform: translateY(-3px); }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0 fw-bold">EXAM PORTAL</h5>
        <small style="font-size: 0.7rem; letter-spacing: 1px;">CBT MANAGEMENT SYSTEM</small>
    </div>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-desktop"></i> Dashboard</a>
        <a href="exam_page.php" class="nav-link"><i class="fas fa-file-signature"></i> Active Exams</a>
        <a href="<?php echo $admit_card_url; ?>" target="_blank" class="nav-link"><i class="fas fa-print"></i> Admit Card</a>
        <a href="result.php" class="nav-link"><i class="fas fa-chart-bar"></i> Results</a>
         <a href="profilee.php?id=<?php echo $student_id; ?>" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profilee.php') ? 'active' : ''; ?>">
    <i class="fas fa-user-circle"></i> MY Profile
</a>
        <a href="logout.php" class="nav-link text-danger mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <nav class="top-nav">
        <button class="btn btn-outline-dark d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('active')">
            <i class="fas fa-bars"></i>
        </button>
        <div class="fw-bold text-uppercase d-none d-sm-block">Examination Dashboard - 2026</div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <div class="fw-bold small"><?php echo $student['name']; ?></div>
                <div class="text-muted" style="font-size: 0.7rem;">IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></div>
            </div>
            <img src="<?php echo $photo_path; ?>" width="45" height="45" class="rounded border border-primary" style="object-fit: cover;">
        </div>
    </nav>

    <div class="content-body">
        <div class="exam-status-bar shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-bullhorn me-2 text-danger"></i> Current Examination Updates</h5>
                    <p class="mb-0 text-muted small">Please verify your details and check for live papers below.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-dark px-3 py-2"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d-M-Y'); ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card-custom">
                    <div class="card-header-custom text-center">CANDIDATE DETAILS</div>
                    <div class="student-profile-box">
                        <img src="<?php echo $photo_path; ?>" class="profile-img-lg shadow-sm" alt="Profile">
                        <h5 class="fw-bold mb-0"><?php echo strtoupper($student['name']); ?></h5>
                        <p class="text-muted small mb-3">Roll No: <?php echo $student['roll_no']; ?></p>
                        <hr>
                        <div class="text-start small">
                            <div class="mb-2"><strong>Course:</strong> <span class="float-end text-primary"><?php echo $student['course'] ?? 'N/A'; ?></span></div>
                            <div class="mb-2"><strong>Email:</strong> <span class="float-end"><?php echo $student['email']; ?></span></div>
                            <div class="mb-0"><strong>Status:</strong> <span class="float-end badge bg-success">Verified</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-custom">
                    <div class="card-header-custom"><i class="fas fa-play-circle me-2"></i>AVAILABLE EXAMINATIONS</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr style="font-size: 0.8rem;">
                                        <th class="ps-4">EXAM NAME</th>
                                        <th>TIMING</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $exam_query = mysqli_query($conn, "SELECT * FROM exams WHERE status IN ('Activ', 'Active')");
                                    $found = false;
                                    while($exam = mysqli_fetch_assoc($exam_query)){
                                        if($current_date == $exam['exam_date'] && $current_time >= $exam['exam_time'] && $current_time <= $exam['exam_end_time']){
                                            $found = true;
                                            echo "<tr>
                                                <td class='ps-4'>
                                                    <span class='fw-bold d-block'>".$exam['exam_name']."</span>
                                                    <span class='badge bg-success' style='font-size: 0.6rem;'>LIVE NOW</span>
                                                </td>
                                                <td class='small text-muted'>
                                                    ".date('h:i A', strtotime($exam['exam_time']))." - ".date('h:i A', strtotime($exam['exam_end_time']))."
                                                </td>
                                                <td class='text-center'>
                                                    <a href='exam_page.php?exam=".urlencode($exam['exam_name'])."' class='btn btn-primary btn-sm px-4'>Launch <i class='fas fa-external-link-alt ms-1'></i></a>
                                                </td>
                                            </tr>";
                                        }
                                    }
                                    if(!$found) {
                                        echo "<tr><td colspan='3' class='text-center py-4 text-muted small'>No examinations scheduled for this time.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="<?php echo $admit_card_url; ?>" class="action-btn text-center shadow-sm">
                            <i class="fas fa-file-pdf fa-2x mb-2 text-danger"></i>
                            <div class="fw-bold small">E-Admit Card</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="results.php" class="action-btn text-center shadow-sm">
                            <i class="fas fa-trophy fa-2x mb-2 text-warning"></i>
                            <div class="fw-bold small">Score Card</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="exam_page.php" class="action-btn text-center shadow-sm">
                            <i class="fas fa-history fa-2x mb-2 text-info"></i>
                            <div class="fw-bold small">Exam History</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>