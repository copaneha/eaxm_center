<?php
include("../config.php"); 
// Sabse naye exams upar dikhane ke liye
$result = mysqli_query($conn,"SELECT * FROM exams ORDER BY exam_date ASC, exam_time ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exam Schedules | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --primary: #2c5ba9; --secondary: #6c757d; --success: #28a745; --danger: #dc3545; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .main-content { margin-left: 260px; padding: 30px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .table thead th { background-color: #f8f9fa; color: #444; font-weight: 600; border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 0.85rem; }
        .exam-row:hover { background-color: #fbfcfe; transition: 0.2s; }
        
        /* Status Badges based on your DB 'status' column */
        .status-active { background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .status-inactive { background: #ffebee; color: #c62828; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        
        .course-badge { background: var(--primary); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; display: inline-block; }
        .subject-text { font-size: 1rem; font-weight: 600; color: #2d3436; display: block; }
        
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark"><i class="fa fa-calendar-check me-2 text-primary"></i>Exam Master List</h3>
            <p class="text-muted mb-0">Manage and track course-wise exam schedules efficiently.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="add-exam.php" class="btn btn-primary px-4 shadow-sm border-0" style="background: var(--primary);">
                <i class="fa fa-plus me-2"></i> Add New Exam
            </a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Exam ID</th>
                        <th>Course & Subject</th>
                        <th>Date & Day</th>
                        <th>Shift Timing</th>
                        <th>Centre & Status</th>
                        <th class="text-center">Students</th>
                        <th class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)){ 
                            // Status check
                            $currentStatus = strtoupper($row['status']);
                    ?>
                    <tr class="exam-row">
                        <td class="ps-4 text-muted fw-bold">#<?= $row['exam_id'] ?></td>
                        
                        <td>
                            <span class="course-badge text-uppercase"><?= $row['exam_name'] ?></span>
                            <span class="subject-text"><?= $row['subject_name'] ?></span>
                        </td>
                        
                        <td>
                            <div class="fw-bold"><?= date('d M, Y', strtotime($row['exam_date'])) ?></div>
                            <div class="small text-muted text-uppercase"><?= date('l', strtotime($row['exam_date'])) ?></div>
                        </td>
                        
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-center border-end pe-2">
                                    <small class="d-block text-muted" style="font-size: 10px;">START</small>
                                    <span class="text-success fw-bold small"><?= date('h:i A', strtotime($row['exam_time'])) ?></span>
                                </div>
                                <div class="text-center">
                                    <small class="d-block text-muted" style="font-size: 10px;">END</small>
                                    <span class="text-danger fw-bold small"><?= date('h:i A', strtotime($row['exam_end_time'])) ?></span>
                                </div>
                            </div>
                        </td>
                        
                        <td>
                            <div class="small fw-semibold mb-1"><i class="fa fa-map-marker-alt text-danger me-1"></i> <?= $row['centre'] ?></div>
                            <span class="<?= ($currentStatus == 'ACTIVE') ? 'status-active' : 'status-inactive' ?>">
                                <?= $currentStatus ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <span class="badge rounded-pill bg-light text-dark border px-3"><?= $row['total_students'] ?></span>
                        </td>
                        
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="edit-exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-sm btn-light border" title="Edit">
                                    <i class="fa fa-pen-to-square text-primary"></i>
                                </a>
                                <a href="delete-exam.php?exam_id=<?= $row['exam_id'] ?>" class="btn btn-sm btn-light border sweet-delete" title="Delete">
                                    <i class="fa fa-trash-can text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa fa-folder-open fa-3x text-light mb-3"></i>
                                <h5 class="text-muted">No Exams Scheduled!</h5>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Delete Confirmation
document.querySelectorAll('.sweet-delete').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.href;
        Swal.fire({
            title: 'Delete karein?',
            text: "Data permanently delete ho jayega!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Haan, uda do!',
            cancelButtonText: 'Nahi'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
</body>
</html>