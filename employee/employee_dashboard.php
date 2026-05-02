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

// Employee Details Query
$query = mysqli_query($conn, "SELECT * FROM employees WHERE id = '$emp_id'");
$row = mysqli_fetch_assoc($query);

$emp_name   = $row['name'] ?? 'Employee';
$emp_role   = $row['role'] ?? 'Staff Member';
$emp_centre = $row['centre'] ?? 'Not Assigned';
$emp_photo  = $row['photo'] ?? 'default.png';

// Duties Data - Next Active Duty
$duty_query = mysqli_query($conn, "SELECT * FROM duties WHERE employee_id = '$emp_id' AND exam_date >= '$today' ORDER BY exam_date ASC LIMIT 1");
$active_duty = mysqli_fetch_assoc($duty_query);

// All Upcoming Duties
$all_duties = mysqli_query($conn, "SELECT * FROM duties WHERE employee_id = '$emp_id' AND exam_date >= '$today' ORDER BY exam_date ASC");
$total_count = mysqli_num_rows($all_duties);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo $emp_name; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #1e3a8a; 
            --secondary: #0ea5e9; 
            --accent: #14b8a6; 
            --dark: #0f172a; 
            --light-bg: #f8fafc; 
            --white: #ffffff; 
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            --sidebar-width: 260px; /* Standard Sidebar Width */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--light-bg); 
            color: var(--dark); 
            line-height: 1.6;
            display: flex; /* Sidebar aur Main content ko side-by-side rakhne ke liye */
        }
       
        /* --- Sidebar Adjustment --- */
        .main-content { 
            flex: 1;
            margin-left: var(--sidebar-width); /* Sidebar ki jagah chhodi */
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
        }

        /* Header Styles */
        .top-header { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px); 
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky; 
            top: 0; 
            z-index: 999; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
        }

        .container { 
            padding: 30px 40px; 
            max-width: 1400px; 
            margin: 0 auto; 
        }

        /* Stats Cards */
        .stat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }

        .stat-card { 
            background: white; 
            padding: 25px; 
            border-radius: 20px; 
            border: 1px solid #f1f5f9; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            box-shadow: var(--shadow); 
        }

        .stat-icon { 
            width: 50px; 
            height: 50px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 20px; 
        }

        /* Hero Card */
        .duty-card-hero { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); 
            color: white; 
            border-radius: 24px; 
            padding: 40px; 
            margin-bottom: 30px; 
            position: relative; 
            overflow: hidden; 
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.2); 
        }

        .duty-card-hero::after { 
            content: "\f073"; 
            font-family: "Font Awesome 6 Free"; 
            font-weight: 900; 
            position: absolute; 
            right: -20px; 
            bottom: -20px; 
            font-size: 180px; 
            opacity: 0.1; 
        }

        /* Table Box */
        .table-box { 
            background: var(--white); 
            border-radius: 24px; 
            padding: 25px; 
            box-shadow: var(--shadow); 
            border: 1px solid #f1f5f9; 
        }

        .table-responsive { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        
        th { 
            text-align: left; 
            padding: 15px; 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }

        td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .status-badge { 
            padding: 5px 12px; 
            border-radius: 8px; 
            font-size: 11px; 
            font-weight: 700; 
            background: #e0f2fe; 
            color: #0369a1; 
        }

        .logout-btn {
            background: #fee2e2;
            color: #ef4444;
            padding: 8px 15px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .logout-btn:hover { background: #fecaca; }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .main-content { 
                margin-left: 0; 
                width: 100%;
            }
        }

        @media (max-width: 640px) { 
            .top-header { padding: 15px 20px; }
            .container { padding: 20px; }
            .duty-card-hero { padding: 25px; } 
            .duty-card-hero h1 { font-size: 1.5rem; } 
            .hide-mobile { display: none; } 
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <header class="top-header">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fas fa-th-large" style="color: var(--primary); font-size: 20px;"></i>
            <h2 style="font-weight: 800; color: var(--dark);">Employee Dashboard</h2>
        </div>
        
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="text-align: right; line-height: 1.2;" class="hide-mobile">
                <strong style="font-size: 0.9rem; display:block;"><?php echo htmlspecialchars($emp_name); ?></strong>
                <span style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($emp_role); ?></span>
            </div>
            
            <img src="../image/<?php echo $emp_photo; ?>" 
                 style="width: 42px; height:42px; border-radius:10px; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);" 
                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($emp_name); ?>&background=random'">
            
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <div class="container">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e0f2fe; color: var(--secondary);"><i class="fas fa-tasks"></i></div>
                <div>
                    <h3 style="font-size: 1.4rem; font-weight: 800;"><?php echo $total_count; ?></h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Upcoming Duties</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ccfbf1; color: var(--accent);"><i class="fas fa-building"></i></div>
                <div>
                    <h3 style="font-size: 0.9rem; font-weight: 800;"><?php echo htmlspecialchars($emp_centre); ?></h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Active Centre</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #eef2ff; color: var(--primary);"><i class="fas fa-user-shield"></i></div>
                <div>
                    <h3 style="font-size: 0.9rem; font-weight: 800;"><?php echo htmlspecialchars($emp_role); ?></h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Job Role</p>
                </div>
            </div>
        </div>

        <?php if($active_duty): ?>
        <div class="duty-card-hero">
            <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Upcoming Exam Duty</span>
            <h1 style="font-size: 2.2rem; margin: 15px 0; font-weight: 800;"><?php echo htmlspecialchars($active_duty['exam_name']); ?></h1>
            <div style="display:flex; flex-wrap:wrap; gap:30px; font-weight: 500;">
                <span><i class="fas fa-calendar-day" style="margin-right: 8px;"></i> <?php echo date('D, d M Y', strtotime($active_duty['exam_date'])); ?></span>
                <span><i class="fas fa-clock" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($active_duty['exam_time']); ?></span>
                <span><i class="fas fa-door-open" style="margin-right: 8px;"></i> Room: <?php echo htmlspecialchars($active_duty['room']); ?></span>
            </div>
        </div>
        <?php else: ?>
        <div style="background:#fff; padding:60px 20px; border-radius:24px; text-align:center; color:#94a3b8; margin-bottom:30px; border:2px dashed #e2e8f0;">
            <i class="fas fa-calendar-check fa-4x" style="margin-bottom: 20px; opacity: 0.2;"></i>
            <h3 style="color: #64748b;">All Caught Up!</h3>
            <p>No new duties have been assigned to you at this time.</p>
        </div>
        <?php endif; ?>

        <div class="table-box">
            <h3 style="margin-bottom: 25px; font-weight: 800; color: var(--dark);">
                <i class="fas fa-clipboard-list" style="color: var(--secondary); margin-right:12px;"></i>Full Duty Schedule
            </h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Exam Description</th>
                            <th>Date</th>
                            <th>Timing</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($all_duties) > 0): ?>
                            <?php while($d = mysqli_fetch_assoc($all_duties)): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--dark);"><?php echo htmlspecialchars($d['exam_name']); ?></div>
                                    <small style="color: #94a3b8;">Ref ID: #<?php echo $d['id']; ?></small>
                                </td>
                                <td><i class="far fa-calendar-alt" style="margin-right: 5px; color: #64748b;"></i> <?php echo date('d M, Y', strtotime($d['exam_date'])); ?></td>
                                <td><i class="far fa-clock" style="margin-right: 5px; color: #64748b;"></i> <?php echo htmlspecialchars($d['exam_time']); ?></td>
                                <td><span style="background: #f1f5f9; padding: 6px 12px; border-radius: 8px; font-weight: 700; color: var(--primary);">Room <?php echo htmlspecialchars($d['room']); ?></span></td>
                                <td><span class="status-badge"><i class="fas fa-check-circle"></i> Assigned</span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 50px; color: #94a3b8;">
                                    <p>No records found in the database.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>