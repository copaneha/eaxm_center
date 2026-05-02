<?php
session_start();
include "config.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * 1. Data Sanitization (Security)
 * POST data ko safe banayein taaki koi hack na kar sake
 */
$studentId = $_SESSION['student_id'];
$examId    = mysqli_real_escape_string($conn, $_POST['exam_id']);
$rating    = mysqli_real_escape_string($conn, $_POST['rating']);
$q1        = mysqli_real_escape_string($conn, $_POST['q1']);
$q2        = mysqli_real_escape_string($conn, $_POST['q2']);
$q3        = mysqli_real_escape_string($conn, $_POST['q3']);

// Check if data is not empty
if (empty($rating) || empty($q1)) {
    $status = "error";
    $message = "Please fill all required fields!";
} else {
    /**
     * 2. Database Insert Logic
     */
    $sql = "INSERT INTO feedback (student_id, exam_id, rating, q1, q2, q3) 
            VALUES ('$studentId', '$examId', '$rating', '$q1', '$q2', '$q3')";
    
    if (mysqli_query($conn, $sql)) {
        $status = "success";
        $message = "Aapka feedback successfully submit ho gaya. Thank you!";
    } else {
        $status = "error";
        $message = "Database Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; }
    </style>
</head>
<body>

<script>
    // 3. SweetAlert2 Professional Implementation
    Swal.fire({
        icon: '<?php echo $status; ?>',
        title: '<?php echo ($status == "success") ? "Done!" : "Oops..."; ?>',
        text: '<?php echo $message; ?>',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // 4. Redirect to Dashboard
            window.location.href = 'dashboard.php';
        }
    });
</script>

</body>
</html>