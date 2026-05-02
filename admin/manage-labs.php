<?php 
include "../config.php"; 
include "sidebar.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Labs | Exam Portal</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #2c52a1;
            --accent: #4a90e2;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #ff7675;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #2d3436;
            --text-muted: #636e72;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            color: var(--text-main); 
        }

        /* --- Responsive Main Container --- */
        .main-container { 
            max-width: 100%; 
            margin: 0 auto; 
            padding: 20px;
            margin-top: 60px;
            transition: all 0.3s ease;
        }

        /* Desktop par sidebar ki jagah (Assuming sidebar 250px-270px hai) */
        @media (min-width: 992px) {
            .main-container { 
                padding-left: 280px; /* Sidebar width + gap */
                padding-right: 30px;
            }
        }

        /* Header Section Responsive */
        .page-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px;
            gap: 15px;
        }

        @media (max-width: 576px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            h3 { font-size: 20px; }
        }

        h3 { 
            font-weight: 700; 
            color: var(--primary); 
            margin: 0; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }

        .btn-add { 
            background: var(--primary); 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 14px;
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.3s ease; 
            box-shadow: 0 4px 15px rgba(44, 82, 161, 0.2);
        }

        /* --- Modern Table Card --- */
        .table-container { 
            background: var(--surface); 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            border: 1px solid #edf2f7;
            overflow: hidden;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto; /* Mobile par scroll ke liye */
            -webkit-overflow-scrolling: touch;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 800px; /* Minimum width taaki mobile par layout collapse na ho */
        }

        th { 
            background: #f8faff; 
            color: var(--text-muted); 
            font-weight: 600; 
            font-size: 11px; 
            text-transform: uppercase; 
            letter-spacing: 0.8px;
            padding: 18px 20px; 
            text-align: left; 
            border-bottom: 2px solid #f1f3f9; 
        }

        td { 
            padding: 16px 20px; 
            border-bottom: 1px solid #f1f3f9; 
            font-size: 14px;
            vertical-align: middle;
        }

        /* --- Badges & Icons --- */
        .lab-code {
            background: #f1f5f9;
            color: #d63384;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
            font-weight: 700;
        }

        .badge { 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 700; 
        }
        .badge-active { background: rgba(0, 184, 148, 0.1); color: var(--success); }
        .badge-maintenance { background: rgba(253, 203, 110, 0.1); color: #d35400; }

        .system-count { 
            background: #eef2ff;
            color: var(--primary); 
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 700; 
            white-space: nowrap;
        }

        /* Action Buttons */
        .btn-delete { 
            background: #fff5f5; 
            color: var(--danger); 
            border: 1px solid #fed7d7;
            padding: 8px 12px; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: all 0.2s; 
        }
        .btn-delete:hover { 
            background: var(--danger); 
            color: white; 
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px !important;
            color: #a0aec0;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="page-header">
        <h3><i class="fa-solid fa-layer-group"></i> Lab Inventory Management</h3>
        <a href="add-lab.php" class="btn-add">
            <i class="fa-solid fa-plus"></i> Add New Lab
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Lab Code</th>
                        <th>Exam Centre</th>
                        <th>Lab Name</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = "SELECT labs.*, exam_centres.centre_name 
                          FROM labs 
                          JOIN exam_centres ON labs.centre_id=exam_centres.id
                          ORDER BY labs.id DESC";
                    $res = mysqli_query($conn, $q);
                    
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)){
                            $statusClass = ($row['status'] == 'Active') ? 'badge-active' : 'badge-maintenance';
                            ?>
                            <tr>
                                <td><span class="lab-code"><?php echo $row['lab_code']; ?></span></td>
                                <td>
                                    <i class="fa-solid fa-building icon-sub"></i>
                                    <strong><?php echo $row['centre_name']; ?></strong>
                                </td>
                                <td><?php echo $row['lab_name']; ?></td>
                                <td>
                                    <i class="fa-solid fa-stairs icon-sub"></i>
                                    <?php echo $row['floor_no']; ?>
                                </td>
                                <td>
                                    <span class="system-count">
                                        <i class="fa-solid fa-desktop icon-sub"></i>
                                        <?php echo $row['total_computers']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-delete" title="Remove Lab">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='empty-state'>
                                <i class='fa-solid fa-folder-open' style='font-size:40px; display:block; margin-bottom:10px;'></i>
                                No labs found in the database.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Confirm Delete with SweetAlert2
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff7675',
        cancelButtonColor: '#636e72',
        confirmButtonText: 'Yes, Delete it!',
        background: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete-lab.php?id=' + id;
        }
    })
}

// Check for Success Message
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('msg') === 'deleted') {
    Swal.fire({
        title: 'Deleted!',
        text: 'The lab has been removed.',
        icon: 'success',
        confirmButtonColor: '#2c52a1'
    });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

</body>
</html>