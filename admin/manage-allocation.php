<?php
include("../config.php");

$query = "SELECT 
            sa.id AS allocation_id,
            sa.seat_no,
            sa.student_id,
            sa.exam_id,
            s.name AS student_name,
            s.roll_no,
            e.exam_name,
            e.exam_date,
            e.exam_time,
            c.centre_name,
            c.city,
            l.lab_name
          FROM seat_allocation sa
          JOIN students s ON sa.student_id = s.student_id
          JOIN exams e ON sa.exam_id = e.exam_id
          JOIN exam_centres c ON sa.centre_id = c.id
          JOIN labs l ON sa.lab_id = l.id
          ORDER BY e.exam_date ASC, sa.id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Seat Allocation</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { --sidebar-width: 260px; --primary-gradient: linear-gradient(135deg, #4361ee, #3f37c9); --bg-light: #f8f9fa; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-light); margin: 0; overflow-x: hidden; }
        
        /* Wrapper & Sidebar Layout */
        .wrapper { display: flex; width: 100%; align-items: stretch; margin-top: 70px; min-height: calc(100vh - 70px); }
        
        #sidebar { 
            min-width: var(--sidebar-width); 
            max-width: var(--sidebar-width); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        #content { width: 100%; padding: 30px; transition: all 0.3s; }

        /* Custom Card & Header */
        .custom-card { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); overflow: hidden; }
        .header-section { background: #fff; padding: 25px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        
        .table-container { padding: 20px; }
        .seat-badge { background: #eef2ff; color: #4361ee; padding: 8px 14px; border-radius: 10px; font-weight: 700; white-space: nowrap; display: inline-block; }
        .btn-add { background: var(--primary-gradient); border: none; padding: 10px 24px; border-radius: 12px; font-weight: 600; color: white; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-add:hover { color: white; opacity: 0.9; transform: translateY(-2px); }

        /* Mobile Responsive Adjustments */
        @media (max-width: 992px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
                position: fixed;
                height: 100%;
            }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
            .header-section { flex-direction: column; align-items: flex-start; }
            .btn-add { width: 100%; text-align: center; }
            .wrapper { margin-top: 20px; }
        }

        @media (max-width: 576px) {
            .table-container { padding: 10px; }
            .btn-sm { padding: 8px 12px; margin-bottom: 5px; width: 100%; }
            .seat-badge { padding: 5px 10px; font-size: 12px; }
        }

        .table-responsive { border: none; }
        table thead th { border: none; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; padding: 15px; }
    </style>
</head>
<body>

<div class="wrapper">
    <div id="sidebar">
        <?php include("sidebar.php"); ?>
    </div>

    <div id="content">
        <button type="button" id="sidebarCollapse" class="btn btn-primary d-lg-none mb-3">
            <i class="fa fa-bars"></i> Menu
        </button>

        <div class="custom-card">
            <div class="header-section">
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">Seat Allocation List</h3>
                    <p class="text-muted small mb-0">Manage and monitor student exam seating arrangements</p>
                </div>
                <a href="allocate-seat.php" class="btn-add">
                    <i class="fa fa-plus-circle me-2"></i> Add New Allocation
                </a>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Exam Details</th>
                                <th>Student</th>
                                <th>Centre & Lab</th>
                                <th>Seat No</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= $row['exam_name'] ?></span>
                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?= date('d M, Y', strtotime($row['exam_date'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold d-block"><?= $row['student_name'] ?></span>
                                        <span class="badge bg-light text-primary border">Roll: <?= $row['roll_no'] ?></span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fa fa-location-dot text-danger me-1"></i> <?= $row['centre_name'] ?><br>
                                            <i class="fa fa-door-open text-primary me-1"></i> <?= $row['lab_name'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="seat-badge"><i class="fa fa-chair me-1"></i> <?= $row['seat_no'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="admit_card.php?student_id=<?= $row['student_id'] ?>&exam_id=<?= $row['exam_id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-id-card"></i> Admit
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm delete-confirm" data-id="<?= $row['allocation_id'] ?>">
                                            <i class="fa fa-trash-can"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Sidebar Toggle
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function (e) {
            if ($(window).width() <= 992) {
                if (!$(e.target).closest('#sidebar, #sidebarCollapse').length) {
                    $('#sidebar').removeClass('active');
                }
            }
        });

        // SweetAlert Delete Confirmation
        $('.delete-confirm').on('click', function (e) {
            var id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this specific allocation?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#4361ee',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete-allocation.php?id=' + id + '&confirm=yes';
                }
            });
        });
    });
</script>
</body>
</html>