<?php
include("../config.php"); 

// URL se 'id' fetch kar rahe hain (Manage page se jo aayega)
if(!isset($_GET['id'])){
    header("Location:manage_exam.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Data fetch kar rahe hain 'exam_id' column ka use karke
$data = mysqli_query($conn, "SELECT * FROM exams WHERE exam_id='$id'");
$row = mysqli_fetch_assoc($data);

// Agar record nahi milta toh wapas bhej dein
if(!$row){
    header("Location:manage_exam.php");
    exit();
}

if(isset($_POST['update_exam'])){
    // SQL Injection se bachne ke liye safe variables
    $name = mysqli_real_escape_string($conn, $_POST['exam_name']);
    $centre = mysqli_real_escape_string($conn, $_POST['centre']);
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];
    $exam_end_time = $_POST['exam_end_time']; 
    $total_students = $_POST['total_students'];

    // UPDATE Query: 'WHERE exam_id=$id' ka use kiya gaya hai
    $update_query = "UPDATE exams SET 
        exam_name='$name',
        exam_date='$exam_date',
        exam_time='$exam_time',
        exam_end_time='$exam_end_time', 
        centre='$centre',
        total_students='$total_students'
        WHERE exam_id='$id'";

    if(mysqli_query($conn, $update_query)){
        // Success message ke saath redirect karein (optional)
        header("Location:manage_exam.php?status=updated");
        exit();
    } else {
        $error_msg = "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam Details | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2c5ba9, #1a3a70);
            --bg-light: #f4f7fa;
            --sidebar-width: 260px;
        }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        .main-content { margin-left: var(--sidebar-width); padding: 50px 30px; }
        .edit-card { background: #fff; border: none; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); max-width: 800px; margin: auto; overflow: hidden; }
        .edit-header { background: var(--primary-gradient); padding: 25px; color: white; text-align: center; }
        .edit-body { padding: 40px; }
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.9rem; }
        .form-control { border: 2px solid #edf2f7; border-radius: 10px; padding: 12px; }
        .form-control:focus { border-color: #2c5ba9; box-shadow: 0 0 0 4px rgba(44, 91, 169, 0.1); }
        .btn-update { background: var(--primary-gradient); color: white; border: none; padding: 14px; border-radius: 10px; font-weight: 600; width: 100%; transition: 0.3s; }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(44, 91, 169, 0.3); color: white; }
        .back-link { color: #718096; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; margin-bottom: 20px; }
        @media (max-width: 991px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="container">
        
        <a href="manage_exam.php" class="back-link">
            <i class="fa fa-chevron-left me-2"></i> Back to Exam List
        </a>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="edit-card">
            <div class="edit-header">
                <h3 class="mb-0 fw-bold"><i class="fa fa-edit me-2"></i> Update Exam Details</h3>
                <small class="opacity-75">Modifying Exam Record ID: #<?= $row['exam_id'] ?></small>
            </div>

            <div class="edit-body">
                <form method="post">
                    <div class="mb-4">
                        <label class="form-label">Exam Title</label>
                        <input type="text" name="exam_name" class="form-control" value="<?= htmlspecialchars($row['exam_name']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Exam Date</label>
                            <input type="date" name="exam_date" class="form-control" value="<?= $row['exam_date'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Starting Time</label>
                            <input type="time" name="exam_time" class="form-control" value="<?= $row['exam_time'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">End Time</label>
                            <input type="time" name="exam_end_time" class="form-control" value="<?= $row['exam_end_time'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Exam Centre / Venue</label>
                        <input type="text" name="centre" class="form-control" value="<?= htmlspecialchars($row['centre']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Total Registered Students</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2"><i class="fa fa-users text-muted"></i></span>
                            <input type="number" name="total_students" class="form-control" value="<?= $row['total_students'] ?>" required>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" name="update_exam" class="btn btn-update">
                            <i class="fa fa-check-circle me-2"></i> Save Changes & Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>