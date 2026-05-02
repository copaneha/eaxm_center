<?php
session_start();
include("../config.php");

// Session Security Management
if(!isset($_SESSION['emp_logged_in'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['emp_id']; 
$today = date('Y-m-d');
$time_now = date('H:i:s');
$message = "";

// Fetch Employee Profile
$emp_name = "Staff Member"; 
$name_query = "SELECT name FROM employees WHERE id = '$user_id'"; 
$name_result = mysqli_query($conn, $name_query);

if($name_result && mysqli_num_rows($name_result) > 0){
    $name_row = mysqli_fetch_assoc($name_result);
    $emp_name = $name_row['name'];
}

// 1. Current Attendance State
$check_query = "SELECT * FROM attendances WHERE emp_id = '$user_id' AND attendance_date = '$today'";
$check_result = mysqli_query($conn, $check_query);
$attendance_data = ($check_result) ? mysqli_fetch_assoc($check_result) : null;

// 2. Attendance Transaction Logic
if (isset($_POST['mark_attendance'])) {
    if (!$attendance_data) {
        // Clock-In Sequence
        $insert_query = "INSERT INTO attendances (emp_id, attendance_date, status, time_in) 
                         VALUES ('$user_id', '$today', 'Present', '$time_now')";
        if (mysqli_query($conn, $insert_query)) {
            $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Clock-In sequence successful.</div>";
            header("Refresh:1");
        }
    } 
    elseif (empty($attendance_data['time_out']) || $attendance_data['time_out'] == "00:00:00") {
        // Clock-Out Sequence
        $update_query = "UPDATE attendances SET time_out = '$time_now' 
                         WHERE emp_id = '$user_id' AND attendance_date = '$today'";
        if (mysqli_query($conn, $update_query)) {
            $message = "<div class='alert info'><i class='fas fa-sign-out-alt'></i> Clock-Out sequence completed.</div>";
            header("Refresh:1");
        }
    }
}

// 3. Historical Data Retrieval
$history_query = "SELECT * FROM attendances WHERE emp_id = '$user_id' ORDER BY attendance_date DESC LIMIT 10";
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkSync | Attendance Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1e1b4b;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --error-red: #ef4444;
            --bg-neutral: #f8fafc;
            --sidebar-width: 280px;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; transition: all 0.2s ease-in-out; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-neutral); 
            margin: 0; padding: 0; color: #1e293b;
            display: flex;
            min-height: 100vh;
        }

        /* --- Main Layout Architecture --- */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #ffffff; 
            padding: 1.5rem 3rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 900;
        }

        .header h2 { margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--primary-dark); }
        .user-badge { background: #f1f5f9; padding: 6px 16px; border-radius: 100px; font-size: 0.85rem; font-weight: 600; color: var(--accent-blue); }

        .container { max-width: 1100px; margin: 2.5rem auto; padding: 0 2rem; width: 100%; }

        /* --- Clock Interface --- */
        .clock-section {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border-radius: 28px; padding: 3rem;
            text-align: center; border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            margin-bottom: 2.5rem;
        }
        #live-clock { font-size: 4.5rem; font-weight: 800; color: var(--primary-dark); margin: 0.5rem 0; letter-spacing: -3px; }
        .date-label { color: var(--accent-blue); font-weight: 700; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 2px; }

        /* --- Dashboard Grid --- */
        .grid-system { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; }
        .action-card { 
            background: #ffffff; padding: 2rem; border-radius: 24px; 
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .action-card h3 { margin: 0 0 1.5rem 0; font-size: 1rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* --- Button Aesthetics --- */
        .btn-action {
            width: 100%; padding: 1.2rem; border: none; border-radius: 16px; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .btn-clock-in { background: var(--success-green); color: white; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); }
        .btn-clock-in:hover { transform: translateY(-3px); filter: brightness(1.1); }
        
        .btn-clock-out { background: var(--error-red); color: white; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }
        .btn-clock-out:hover { transform: translateY(-3px); filter: brightness(1.1); }
        
        .btn-disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; border: 2px dashed #cbd5e1; }

        /* --- Activity Logs Table --- */
        .logs-wrapper { margin-top: 3rem; background: #fff; border-radius: 24px; padding: 2rem; border: 1px solid #e2e8f0; }
        .logs-wrapper h3 { font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--primary-dark); }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        th { text-align: left; padding: 1rem; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
        td { padding: 1.2rem 1rem; background: #f8fafc; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
        td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* --- Responsive Logic --- */
        @media (max-width: 1024px) {
            .main-wrapper { margin-left: 0; width: 100%; }
            .header { padding: 1rem 1.5rem; }
            #live-clock { font-size: 3rem; }
            .container { padding: 0 1rem; margin: 1.5rem auto; }
        }

        /* --- Alerts --- */
        .alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .success { background: #f0fdf4; color: #166534; border-left: 4px solid var(--success-green); }
        .info { background: #f0f9ff; color: #075985; border-left: 4px solid var(--accent-blue); }
    </style>
</head>
<body>

    <?php include("sidebar.php"); ?>

    <div class="main-wrapper">
        <header class="header">
            <h2><i class="fas fa-fingerprint" style="color: var(--accent-blue); margin-right: 10px;"></i> Attendance Management</h2>
            <div class="user-badge">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($emp_name); ?>
            </div>
        </header>

        <div class="container">
            <div class="clock-section">
                <div class="date-label" id="live-date">Initializing...</div>
                <div id="live-clock">00:00:00</div>
                <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Official Enterprise Server Time</div>
            </div>

            <?php echo $message; ?>

            <div class="grid-system">
                <div class="action-card">
                    <h3>Authentication</h3>
                    <form method="POST">
                        <?php if (!$attendance_data): ?>
                            <button type="submit" name="mark_attendance" class="btn-action btn-clock-in">
                                <i class="fas fa-sign-in-alt"></i> EXECUTE CLOCK-IN
                            </button>
                        <?php elseif (empty($attendance_data['time_out']) || $attendance_data['time_out'] == "00:00:00"): ?>
                            <button type="submit" name="mark_attendance" class="btn-action btn-clock-out">
                                <i class="fas fa-sign-out-alt"></i> EXECUTE CLOCK-OUT
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-action btn-disabled">
                                <i class="fas fa-check-double"></i> DUTY CONCLUDED
                            </button>
                        <?php endif; ?>
                    </form>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 1rem; text-align: center;">Note: Please ensure you are within the designated workstation area.</p>
                </div>

                <div class="action-card">
                    <h3>Current Shift Details</h3>
                    <div style="margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                            <span style="color: #64748b; font-size: 0.9rem;">Arrival Time:</span>
                            <span style="font-weight: 700; color: var(--success-green);"><?php echo $attendance_data['time_in'] ?? 'Not Logged'; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                            <span style="color: #64748b; font-size: 0.9rem;">Departure Time:</span>
                            <span style="font-weight: 700; color: var(--error-red);"><?php echo (!empty($attendance_data['time_out']) && $attendance_data['time_out'] != "00:00:00") ? $attendance_data['time_out'] : 'Active Session'; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                            <span style="color: #64748b; font-size: 0.9rem;">Session Status:</span>
                            <span style="font-weight: 800; color: var(--accent-blue); font-size: 0.8rem; background: #eff6ff; padding: 2px 10px; border-radius: 5px;">
                                <?php echo strtoupper($attendance_data['status'] ?? 'No Active Session'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="logs-wrapper">
                <h3><i class="fas fa-history"></i> Recent Activity Logs</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock-In</th>
                                <th>Clock-Out</th>
                                <th>Designation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($history_result && mysqli_num_rows($history_result) > 0): ?>
                                <?php while($h = mysqli_fetch_assoc($history_result)): ?>
                                <tr>
                                    <td style="font-weight:700; color: var(--primary-dark);">
                                        <?php echo date('D, d M Y', strtotime($h['attendance_date'])); ?>
                                    </td>
                                    <td style="font-weight:600; color: var(--success-green);"><?php echo $h['time_in']; ?></td>
                                    <td style="font-weight:600; color: var(--error-red);">
                                        <?php echo ($h['time_out'] == "00:00:00") ? '--:--' : $h['time_out']; ?>
                                    </td>
                                    <td>
                                        <span style="font-size: 10px; letter-spacing: 1px; font-weight: 800; color: #64748b;">OFFICIAL LOG</span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center; padding: 3rem; color: #94a3b8;">No historical data available for this period.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer style="margin-top: auto; padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.75rem;">
            &copy; 2026 WorkSync Enterprise Solutions. All operational logs are timestamped and encrypted.
        </footer>
    </div>

    <script>
        function updateLiveTime() {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            
            document.getElementById('live-clock').textContent = now.toLocaleTimeString('en-GB', timeOptions);
            document.getElementById('live-date').textContent = now.toLocaleDateString('en-US', dateOptions);
        }
        setInterval(updateLiveTime, 1000);
        updateLiveTime();
    </script>
</body>
</html>