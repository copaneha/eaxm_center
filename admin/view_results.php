<?php
include("../config.php"); 
include "sidebar.php"; 

// Query to get results with is_issued and issued_at fields
$query = "SELECT res.*, s.name as student_name, s.roll_no, e.exam_name 
          FROM exam_submissions res
          JOIN students s ON res.student_id = s.student_id
          JOIN exams e ON res.exam_id = e.exam_id
          ORDER BY res.submitted_at DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("<div style='margin-left:270px; padding:100px;'><h3>SQL Query Failed!</h3><p>" . mysqli_error($conn) . "</p></div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-wrapper { margin-left: 260px; padding: 90px 25px; background-color: #f4f7f6; min-height: 100vh; }
        .result-card { background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; }
        .table thead { background-color: #2b5a9e; color: white; }
        .status-issued { background-color: #d1f7e8; color: #157347; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-pending { background-color: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        @media (max-width: 768px) { .main-wrapper { margin-left: 0; } }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="container-fluid">
        <div class="result-card">
            <h2 class="h4 mb-4 text-gray-800"><i class="fas fa-poll me-2 text-primary"></i> Examination Records</h2>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Details</th>
                            <th>Exam</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Status</th>
                            <th class="text-center no-print" style="width: 250px;">Action / Issue Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="text-center">#<?php echo $row['id']; ?></td>
                            <td>
                                <b><?php echo htmlspecialchars($row['student_name']); ?></b><br>
                                <small class="text-muted">Roll: <?php echo $row['roll_no']; ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                            <td class="text-center"><span class="badge bg-primary"><?php echo $row['score']; ?>%</span></td>
                            
                            <td class="text-center">
                                <?php if($row['is_issued'] == 1): ?>
                                    <span class="status-issued">ISSUED</span><br>
                                    <small><?php echo date('d-m-Y', strtotime($row['issued_at'])); ?></small>
                                <?php else: ?>
                                    <span class="status-pending">PENDING</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center no-print">
                                <?php if($row['is_issued'] == 0): ?>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="date" id="date_<?php echo $row['id']; ?>" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                        <button class="btn btn-success" onclick="issueResult(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-paper-plane"></i> Issue
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <div class="btn-group w-100">
                                    <a href="generate_result.php?submission_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function issueResult(id) {
        var selectedDate = document.getElementById('date_' + id).value;
        
        if(!selectedDate) {
            Swal.fire('Error', 'Please select a date first!', 'error');
            return;
        }

        Swal.fire({
            title: 'Issue Result?',
            text: "Student will see this result as declared on " + selectedDate,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Yes, Publish!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirecting with ID and selected Date
                window.location.href = 'process_issue.php?id=' + id + '&date=' + selectedDate;
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = 'delete_result.php?id=' + id;
        });
    }
</script>
</body>
</html>