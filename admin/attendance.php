<?php
include("../config.php"); 
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$today = date('Y-m-d');
$count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM attendances WHERE attendance_date = '$today'");
$count_data = mysqli_fetch_assoc($count_res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management | Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root { 
            --primary: #1a237e; 
            --accent: #ff5722;
            --bg: #f8fafc; 
            --white: #ffffff; 
            --text-main: #334155;
            --border: #e2e8f0;
            --shadow: 0 4px 20px rgba(0,0,0,0.05);
            --sidebar-width: 260px; 
        }

        * { box-sizing: border-box; }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg); 
            margin: 0; 
            color: var(--text-main);
            /* Sidebar aur Main content ko align karne ke liye */
        }

        /* --- CONTENT WRAPPER --- */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
         margin-top:30px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* --- TOP BAR --- */
        .top-bar { 
            background: var(--white); 
            color: var(--primary); 
            height: 70px;
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 30px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .logo-text { font-weight: 700; font-size: 1.2rem; letter-spacing: -0.5px; }

        .exit-btn {
            background: #fee2e2;
            color: #ef4444;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .exit-btn:hover { background: #ef4444; color: white; }

        /* --- CONTAINER --- */
        .container { 
            flex: 1;
            padding: 30px; 
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        .page-header { 
            margin-bottom: 30px; 
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .page-header .icon-box {
            background: var(--primary);
            color: white;
            padding: 10px;
            border-radius: 10px;
        }
        .page-header h2 { margin: 0; font-size: 22px; font-weight: 700; color: #1e293b; }

        /* --- STATS --- */
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
            gap: 25px; 
            margin-bottom: 35px; 
        }

        .stat-card { 
            background: var(--white); 
            padding: 25px; 
            border-radius: 16px; 
            display: flex; 
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #eef2ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        .stat-info span { font-size: 0.85rem; color: #64748b; font-weight: 500; display: block; }
        .stat-info b { font-size: 1.4rem; color: #1e293b; display: block; margin-top: 2px; }

        /* --- TABLE CARD --- */
        .table-card { 
            background: var(--white); 
            border-radius: 16px; 
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 80px;
        }

        .table-header { 
            padding: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 15px;
        }
        .table-header h4 { margin: 0; font-weight: 600; color: #1e293b; }

        .search-box { 
            padding: 10px 15px; 
            border: 1px solid var(--border); 
            border-radius: 8px;
            width: 250px;
            outline: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .search-box:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1); }

        .table-responsive { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { 
            background: #f8fafc; 
            text-align: left; 
            padding: 18px 20px; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            color: #64748b;
            font-weight: 600;
        }
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f1f5f9; }

        .badge-present { 
            background: #dcfce7; 
            color: #15803d; 
            padding: 6px 12px; 
            border-radius: 6px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            display: inline-block;
        }

        /* --- BOTTOM BAR --- */
        .bottom-bar { 
            background:  #10438f;
            padding: 15px 30px; 
            border-top: 1px solid var(--border);
            display: flex; 
            justify-content: center;
            font-size: 0.85rem;
            color: white;
        }

        /* --- MOBILE RESPONSIVE --- */
        @media (max-width: 992px) {
            :root { --sidebar-width: 0px; }
            body { display: block; }
            .main-content { margin-left: 0; width: 100%; }
            /* Sidebar hidden logic should be inside sidebar.php (hamburger menu) */
        }

        @media (max-width: 576px) {
            .container { padding: 15px; }
            .top-bar { padding: 0 15px; }
            .search-box { width: 100%; }
            .table-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>

<body>

    <?php include "sidebar.php"?>

    <div class="main-content">
        

        <div class="container">
            <div class="page-header">
                <div class="icon-box"><i class="fa-solid fa-calendar-check"></i></div>
                <h2>Attendance Management</h2>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
                    <div class="stat-info">
                        <span>Today's Date</span>
                        <b><?php echo date('d M, Y'); ?></b>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users-gear"></i></div>
                    <div class="stat-info">
                        <span>Staff On Duty</span>
                        <b><?php echo $count_data['total'] ?? 0; ?></b>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff7ed; color:#ea580c;"><i class="fa-solid fa-clock"></i></div>
                    <div class="stat-info">
                        <span>Current Live Time</span>
                        <b id="liveClock">00:00:00</b>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fa-solid fa-list-ul me-2"></i>Attendance Logs</h4>
                    <input type="text" id="attendanceSearch" class="search-box" placeholder="Search staff name...">
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Staff Name</th>
                                <th>Date</th>
                                <th><i class="fa-solid fa-right-to-bracket text-success me-1"></i> Time In</th>
                                <th><i class="fa-solid fa-right-from-bracket text-danger me-1"></i> Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody id="attendanceTable">
                        <?php
                        $sql = "SELECT attendances.*, employees.name 
                                FROM attendances 
                                JOIN employees ON attendances.emp_id = employees.id 
                                ORDER BY attendances.id DESC";

                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){
                                echo "<tr>
                                    <td><span class='fw-bold text-primary'>#{$row['emp_id']}</span></td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['attendance_date']}</td>
                                    <td><span class='text-success'>{$row['time_in']}</span></td>
                                    <td><span class='text-danger'>".($row['time_out'] ?: '--:--')."</span></td>
                                    <td><span class='badge-present'>{$row['status']}</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No attendance records found for today.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
    </div>

    <script>
    // Live Clock Function
    function updateClock() {
        const now = new Date();
        document.getElementById('liveClock').innerHTML = now.toLocaleTimeString('en-US', { hour12: true });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Live Search Functionality
    document.getElementById('attendanceSearch').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        let rows = document.querySelectorAll("#attendanceTable tr");
        
        rows.forEach(row => {
            // Hum sirf Staff Name column ko search kar rahe hain (index 1)
            let staffName = row.cells[1] ? row.cells[1].innerText.toLowerCase() : "";
            row.style.display = staffName.includes(val) ? "" : "none";
        });
    });
    </script>

</body>
</html>