<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: login.php");
    exit();
}

$sql = "
    SELECT a.*, s.name AS student_name, e.exam_name 
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    JOIN exams e ON a.exam_id = e.exam_id
    ORDER BY a.id DESC
";

$query = mysqli_query($conn, $sql);

if(!$query){
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Attendance | Online Exam Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --bg-color: #f8f9fc;
            --text-dark: #2d3436;
            --sidebar-width: 260px;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            margin: 0;
        }

        /* --- Main Content Layout --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s ease;
           
        }

        /* --- Card Container --- */
        .table-container {
            background: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-flex h2 {
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            font-size: 24px;
        }

        /* --- Search Bar Styling --- */
        .search-wrapper {
            position: relative;
            width: 300px;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .search-box {
            padding-left: 45px;
            height: 45px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        /* --- Table Design --- */
        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #64748b;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px;
        }

        .table tbody tr {
            background-color: #fff;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .table tbody tr:hover {
            transform: scale(1.005);
            background-color: #f8faff !important;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        /* --- Status Badge --- */
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .ip-text {
            color: #718096;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        /* --- Mobile Responsive --- */
        @media(max-width: 992px){
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .search-wrapper {
                width: 100%;
            }
            .header-flex {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<?php include "sidebar.php"?>

<div class="main-content">
    <div class="container-fluid">
        <div class="table-container">
            
            <div class="header-flex">
                <h2><i class="fa-solid fa-clipboard-user text-primary me-2"></i> Student Attendance</h2>
                
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="Search Student or Exam...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Exam Name</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th><i class="fa-regular fa-clock me-1"></i> Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    $i = 1;
                    if(mysqli_num_rows($query) > 0){
                        while($row = mysqli_fetch_assoc($query)){
                    ?>
                        <tr>
                            <td><span class="text-muted fw-bold"><?= $i++; ?></span></td>
                            <td><div class="fw-semibold"><?= htmlspecialchars($row['student_name']); ?></div></td>
                            <td><?= htmlspecialchars($row['exam_name']); ?></td>
                            <td>
                                <span class="status-badge">
                                    <i class="fa-solid fa-circle-check me-1"></i> <?= $row['status']; ?>
                                </span>
                            </td>
                            <td><span class="ip-text"><?= $row['ip_address']; ?></span></td>
                            <td class="text-secondary small"><?= date('d M, Y | h:i A', strtotime($row['punch_time'])); ?></td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No attendance records found.</td></tr>";
                    }
                    ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
// Efficient Search Logic
document.getElementById("searchInput").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#attendanceTable tbody tr");

    rows.forEach(row => {
        let name = row.cells[1].innerText.toLowerCase();
        let exam = row.cells[2].innerText.toLowerCase();
        
        if(name.includes(value) || exam.includes(value)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<?php
if(!isset($_SESSION['attendance_alert_shown'])){
    $_SESSION['attendance_alert_shown'] = true;
?>
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Attendance Data Loaded',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});
</script>
<?php } ?>

</body>
</html>