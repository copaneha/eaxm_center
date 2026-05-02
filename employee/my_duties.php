<?php
session_start();
include("../config.php"); 

// Session Check
if(!isset($_SESSION['emp_logged_in']) || $_SESSION['emp_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['emp_id'];
$today = date('Y-m-d');

// 1. Upcoming Duties
$upcoming_sql = "SELECT * FROM duties WHERE employee_id = '$emp_id' AND exam_date >= '$today' ORDER BY exam_date ASC";
$upcoming_res = mysqli_query($conn, $upcoming_sql);

// 2. Past Duties
$past_sql = "SELECT * FROM duties WHERE employee_id = '$emp_id' AND exam_date < '$today' ORDER BY exam_date DESC";
$past_res = mysqli_query($conn, $past_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exam Duties | Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --primary: #1a237e; 
            --bg: #f4f7f9; 
            --white: #ffffff; 
            --sidebar-width: 260px; /* Sidebar ki width define ki */
        }
        
        * { box-sizing: border-box; }

        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            background: var(--bg); 
            display: flex; /* Sidebar aur Content ko side-by-side rakhne ke liye */
        }
        
        /* --- Sidebar Adjustment (Exactly like Dashboard) --- */
        .main-content { 
            flex: 1; 
            margin-left: var(--sidebar-width); /* Sidebar ke liye jagah chhodi */
            padding: 30px; 
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
        }

        .duty-section { 
            background: white; 
            border-radius: 12px; 
            padding: 25px; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
        }

        .section-title { 
            border-bottom: 2px solid #eee; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            color: var(--primary); 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }

        .section-title h3 { margin: 0; font-size: 1.2rem; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f8f9fa; color: #666; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-active { background: #e3f2fd; color: #1976d2; }
        .badge-completed { background: #e8f5e9; color: #2e7d32; }
        
        .room-tag { background: #f1f3f4; padding: 4px 10px; border-radius: 6px; font-weight: bold; color: #333; }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content { 
                margin-left: 0; 
                width: 100%; 
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <h2 style="margin-bottom: 25px; color: var(--primary);">
        <i class="fas fa-tasks"></i> My Examination Duties
    </h2>

    <div class="duty-section">
        <div class="section-title">
            <i class="fas fa-clock" style="color:#1976d2;"></i>
            <h3>Upcoming & Today's Duties</h3>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Room / Hall</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($upcoming_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($upcoming_res)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['exam_name']); ?></strong></td>
                            <td><?php echo date('d M, Y', strtotime($row['exam_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['exam_time']); ?></td>
                            <td><span class="room-tag"><?php echo htmlspecialchars($row['room']); ?></span></td>
                            <td><span class="status-badge badge-active">Active</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; color:#999; padding:40px;">No upcoming duties assigned.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="duty-section">
        <div class="section-title">
            <i class="fas fa-history" style="color:#2e7d32;"></i>
            <h3>Duty History (Past)</h3>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($past_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($past_res)): ?>
                        <tr>
                            <td style="color:#777;"><?php echo htmlspecialchars($row['exam_name']); ?></td>
                            <td style="color:#777;"><?php echo date('d M, Y', strtotime($row['exam_date'])); ?></td>
                            <td style="color:#777;"><?php echo htmlspecialchars($row['exam_time']); ?></td>
                            <td style="color:#777;"><?php echo htmlspecialchars($row['room']); ?></td>
                            <td><span class="status-badge badge-completed">Completed</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; color:#999; padding:40px;">No duty history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>