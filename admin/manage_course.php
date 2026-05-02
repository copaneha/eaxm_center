<?php
ob_start(); // Output Buffering start (Header error fix karne ke liye)
include("../config.php"); 
include("sidebar.php"); 
// --- DELETE LOGIC (Isse sidebar se upar rakhein) ---
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM courses WHERE id = $id";
    if(mysqli_query($conn, $delete_query)) {
        header("Location: manage_course.php?msg=deleted");
        exit();
    } else {
        header("Location: manage_course.php?msg=error");
        exit();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --success: #10b981;
            --danger: #ef4444;
            --sidebar-width: 280px;
        }

        body { 
            background: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b;
        }

        .main-wrapper {
            padding: 35px;
            transition: all 0.3s ease;
            margin-top:50px;
        }

        @media (min-width: 992px) {
            .main-wrapper { margin-left: var(--sidebar-width); }
        }

        .header-container {
            background: #fff;
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; margin: 0; }

        .data-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);
        }

        .table thead { background: #f1f5f9; }
        .table thead th {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
            padding: 18px 24px;
            border: none;
        }

        .table tbody tr { transition: 0.2s; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:hover { background-color: #f8fafc; }

        .table td { padding: 20px 24px; vertical-align: middle; border: none; }

        .course-badge {
            width: 45px; height: 45px;
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.2rem;
        }

        .price-label { font-weight: 800; color: #0f172a; font-size: 1.1rem; }
        
        .duration-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .action-btn {
            width: 38px; height: 38px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center; justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .btn-edit { color: var(--primary); }
        .btn-edit:hover { background: var(--primary); color: white; border-color: var(--primary); }

        .btn-del { color: var(--danger); }
        .btn-del:hover { background: var(--danger); color: white; border-color: var(--danger); }

        .btn-add-new {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.3);
            transition: 0.3s;
        }

        .btn-add-new:hover { background: #4338ca; color: white; transform: translateY(-2px); }

        @media (max-width: 768px) {
            .header-container { flex-direction: column; text-align: center; gap: 15px; }
            .main-wrapper { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="container-fluid">
        
        <div class="header-container">
            <div>
                <h2 class="page-title">Manage Programs</h2>
                <p class="text-muted small m-0">View, update, or remove courses from the database.</p>
            </div>
            <a href="add_course.php" class="btn-add-new">
                <i class="fas fa-plus me-2"></i> Add New Course
            </a>
        </div>

        <div class="data-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course Details</th>
                            <th>Duration</th>
                            <th>Fees</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM courses ORDER BY id DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-badge me-3">
                                                <?php echo strtoupper(substr($row['course_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $row['course_name']; ?></div>
                                                <div class="text-muted small" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo $row['course_description']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="duration-tag">
                                            <i class="far fa-calendar-alt me-1"></i> <?php echo $row['duration']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="price-label">₹<?php echo number_format($row['fees'], 0); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit_course.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit me-1" title="Edit Course">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="action-btn btn-del" title="Delete Course">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center py-5 text-muted">No courses found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove this course!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete it!',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'manage_course.php?delete=' + id;
            }
        })
    }

    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('msg') === 'deleted') {
        Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Course has been removed.', timer: 2000, showConfirmButton: false, borderRadius: '15px' });
    } else if(urlParams.get('msg') === 'error') {
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.', borderRadius: '15px' });
    }
</script>

</body>
</html>
<?php 
ob_end_flush(); // Output buffer ko flush karein
?>