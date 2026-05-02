<?php 
include ("../config.php"); 
// Database connection check
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$latest_res = mysqli_query($conn, "SELECT id FROM employees ORDER BY id DESC LIMIT 1");
$latest = mysqli_fetch_assoc($latest_res);
$latest_id = ($latest) ? $latest['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1a3a5f;
            --header-bg: #2b5a9e;
            --body-bg: #f0f4f8;
            --white: #ffffff;
            --blue: #2b5a9e;
            --green: #2e7d32;
            --red: #dc3545;
            --teal: #17a2b8;
            --orange: #ef6c00;
        }

        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: var(--body-bg); }
        
        /* Responsive Main Content */
        .main-content { 
            margin-left: 260px; /* Sidebar space for desktop */
            padding: 20px;
            margin-top: 20px;
            transition: 0.3s;
        } 
        
        .header { background: var(--header-bg); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .logout-btn { background: var(--red); color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; }
        
        .container { padding: 10px; max-width: 100%; }
        .page-title { margin-bottom: 25px; color: #333; font-size: 28px; }

        .action-cards-top { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .btn-action { padding: 12px 20px; border-radius: 6px; text-decoration: none; color: white; font-weight: 500; display: flex; align-items: center; gap: 10px; transition: 0.3s; }
        .btn-action:hover { opacity: 0.9; transform: translateY(-2px); }
        
        .bg-blue { background: var(--blue); }
        .bg-orange { background: var(--orange); }

        .table-card { background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        .table-header { padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        
        .search-box { padding: 10px 15px; border: 2px solid #dfe6e9; border-radius: 25px; width: 300px; outline: none; transition: 0.3s; font-size: 14px; max-width: 100%; }
        .search-box:focus { border-color: var(--blue); box-shadow: 0 0 8px rgba(43, 90, 158, 0.2); }

        /* Mobile Responsive Table Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table { width: 100%; border-collapse: collapse; min-width: 700px; } /* Ensures table doesn't squash too much */
        th { text-align: left; padding: 15px 20px; color: #666; font-weight: 600; border-bottom: 2px solid #eee; background: #fdfdfd; }
        td { padding: 12px 20px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .emp-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; }
        .emp-name { font-weight: bold; display: block; color: #2b5a9e; }
        .emp-subtext { font-size: 12px; color: #888; }

        .row-btns { display: flex; gap: 5px; flex-wrap: wrap; }
        .btn-small { padding: 6px 10px; border-radius: 4px; font-size: 12px; text-decoration: none; color: white; display: flex; align-items: center; gap: 5px; }
        .bg-teal { background: #00796b; }
        .bg-red { background: #c62828; }
        .bg-navy { background: #1a3a5f; }

        /* Media Queries for Responsiveness */
        @media (max-width: 992px) {
            .main-content { margin-left: 0; width: 100%; padding: 15px; box-sizing: border-box; }
        }

        @media (max-width: 600px) {
            .page-title { font-size: 22px; }
            .btn-action { width: 100%; justify-content: center; }
            .table-header { flex-direction: column; align-items: stretch; }
            .search-box { width: 100%; }
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="container">
        <h2 class="page-title">Manage Employees</h2>

        <div class="action-cards-top">
            <a href="add_employee.php" class="btn-action bg-blue"><i class="fa fa-plus"></i> Add Employee</a>
            <a href="attendance.php" class="btn-action bg-orange"><i class="fa fa-clipboard-list"></i> Attendance Report</a>
        </div>

        <div class="table-card">
            <div class="table-header">
                <strong>Employee List</strong>
                <div style="position: relative;">
                    <i class="fa fa-search" style="position: absolute; right: 15px; top: 12px; color: #aaa;"></i>
                    <input type="text" id="empSearch" class="search-box" placeholder="Search name, role, email...">
                </div>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name & Role</th>
                            <th>Email</th>
                            <th>Centre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="empTable">
                        <?php
                        $q = mysqli_query($conn, "SELECT * FROM employees ORDER BY id DESC");
                        if(mysqli_num_rows($q) > 0){
                            while($row = mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <td><img src="../image/<?php echo $row['photo']; ?>" class="emp-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'"></td>
                            <td>
                                <span class="emp-name"><?php echo htmlspecialchars($row['name']); ?></span>
                                <span class="emp-subtext"><?php echo htmlspecialchars($row['role']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['centre']); ?></td>
                            <td>
                                <div class="row-btns">
                                    <a href="attendance.php?id=<?php echo $row['id']; ?>" class="btn-small bg-navy"><i class="fa fa-eye"></i> Attendance</a>
                                    <a href="update_employee.php?id=<?php echo $row['id']; ?>" class="btn-small bg-blue"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="assign_duty.php?id=<?php echo $row['id']; ?>" class="btn-small bg-teal"><i class="fa fa-user-check"></i> Duty</a>
                                    <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn-small bg-red sweet-delete"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No employees found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- LIVE SEARCH LOGIC ---
    document.getElementById('empSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#empTable tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = ""; 
            } else {
                row.style.display = "none"; 
            }
        });
    });

    // --- SWEET ALERT DELETE ---
    document.querySelectorAll('.sweet-delete').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            let link = this.getAttribute("href");
            Swal.fire({
                title: 'Are you sure?',
                text: "Employee ka record hamesha ke liye delete ho jayega!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#2b5a9e',
                confirmButtonText: 'Yes, Delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });
    });
</script>

</body>
</html>