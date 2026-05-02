<?php
session_start();

// SECURITY CHECK: Agar admin logged in nahi hai toh login page par bhejo
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Browser Cache Clear Headers (Back button dabane par purana data na dikhe)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include("../config.php");

/**
 * Safe Count Function
 */
function getCountSafely($conn, $table) {
    $sql = "SELECT COUNT(*) as total FROM `$table`";
    $res = mysqli_query($conn, $sql);
    if($res) {
        $data = mysqli_fetch_assoc($res);
        return $data['total'];
    } else {
        return "0"; 
    }
}

// 2. DYNAMIC COUNTS
$exams_count     = getCountSafely($conn, "exams");
$students_count  = getCountSafely($conn, "students");
$employees_count = getCountSafely($conn, "employees");
$centers_count   = getCountSafely($conn, "exam_centres"); 

// 3. RECENT EXAMS
$recent_exams_query = mysqli_query($conn, "SELECT exam_name, exam_date FROM exams ORDER BY id DESC LIMIT 5");

// 4. SYSTEM OVERVIEW
$today = date('Y-m-d');
$active_exams_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM exams WHERE exam_date = '$today'");
$active_exams = ($active_exams_res) ? mysqli_fetch_assoc($active_exams_res)['total'] : 0;

$upcoming_exams_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM exams WHERE exam_date > '$today'");
$upcoming_exams = ($upcoming_exams_res) ? mysqli_fetch_assoc($upcoming_exams_res)['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    :root {
        --sidebar-width: 260px;
        --primary-dark: #1e293b;
        --bg-light: #f8fafc;
    }

    body { 
        margin: 0; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        background: var(--bg-light); 
    }
    
    /* Layout Adjustment - Isko 1050px se badha kar 100% ya 1400px kar diya */
    .main-wrapper { 
        width: 100%;
        max-width: 1900px; /* Width badha di gayi hai */
        margin: 0 auto;
        margin-top: 100px;
    }

    .main-content {
        margin-left: var(--sidebar-width); 
        padding: 0 20px; /* Side mein thodi space ke liye */
        width: calc(100% - var(--sidebar-width)); /* Isse width poori fill hogi */
    }

    /* Responsive for mobile */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
    
    /* Stats Card Styling */
    .stat-card {
        border: none;
        border-radius: 15px;
        padding: 25px;
        color: white;
        transition: transform 0.3s;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .blue { background: linear-gradient(135deg, #4361ee, #3f37c9); }
    .green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .orange { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .purple { background: linear-gradient(135deg, #9b59b6, #8e44ad); }

    .panel-card {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        padding: 25px;
        height: 100%;
    }

    .status-pill {
        background: #f1f5f9;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
</head>
<body>

<div class="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Welcome, Admin</h2>
                    <p class="text-muted small mb-0">Overview of your management system</p>
                </div>
                <span class="badge bg-white text-dark shadow-sm p-2 px-3 rounded-pill border">
                    <i class="far fa-calendar-alt me-2 text-primary"></i><?php echo date('d M, Y'); ?>
                </span>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card blue">
                        <div><p class="mb-1 opacity-75">Total Exams</p><h2 class="fw-bold mb-0"><?php echo $exams_count; ?></h2></div>
                        <i class="fas fa-file-signature fa-2x opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card green">
                        <div><p class="mb-1 opacity-75">Students</p><h2 class="fw-bold mb-0"><?php echo $students_count; ?></h2></div>
                        <i class="fas fa-user-graduate fa-2x opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card orange">
                        <div><p class="mb-1 opacity-75">Employees</p><h2 class="fw-bold mb-0"><?php echo $employees_count; ?></h2></div>
                        <i class="fas fa-users-cog fa-2x opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card purple">
                        <div><p class="mb-1 opacity-75">Centres</p><h2 class="fw-bold mb-0"><?php echo $centers_count; ?></h2></div>
                        <i class="fas fa-map-marker-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-7">
                    <div class="panel-card">
                        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-history me-2 text-primary"></i> Recent Exams List</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($recent_exams_query && mysqli_num_rows($recent_exams_query) > 0): 
                                        while($row = mysqli_fetch_assoc($recent_exams_query)): ?>
                                        <tr>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($row['exam_name']); ?></td>
                                            <td class="text-muted"><?php echo date('d M Y', strtotime($row['exam_date'])); ?></td>
                                            <td><span class="badge bg-success-subtle text-success border border-success px-3">Completed</span></td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No exams found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="panel-card">
                        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-microchip me-2 text-primary"></i> System Status</h5>
                        
                        <div class="status-pill">
                            <span>Server Status</span>
                            <span class="text-success fw-bold"><i class="fas fa-circle me-1 small"></i> Online</span>
                        </div>
                        <div class="status-pill">
                            <span>Exams Today</span>
                            <span class="fw-bold"><?php echo $active_exams; ?></span>
                        </div>
                        <div class="status-pill">
                            <span>Upcoming Schedule</span>
                            <span class="fw-bold text-primary"><?php echo $upcoming_exams; ?></span>
                        </div>
                        <div class="status-pill">
                            <span>Database Health</span>
                            <span class="text-success fw-bold small">Connected</span>
                        </div>
                        
                        <div class="mt-4 p-3 bg-primary-subtle rounded border border-primary-subtle text-primary small">
                            <i class="fas fa-info-circle me-2"></i> All systems are running smoothly.
                        </div>
                    </div>
                </div>
            </div> </div> </div> </div> </body>
</html>