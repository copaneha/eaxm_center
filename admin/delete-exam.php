<?php
include("../config.php"); 

// 1. URL se 'exam_id' receive karein
if(!isset($_GET['exam_id'])) {
    header("Location:manage_exam.php");
    exit();
}

$exam_id = $_GET['exam_id'];

// 2. Jab user SweetAlert mein 'Confirm' click kare tab delete karein
if(isset($_GET['confirm']) && $_GET['confirm'] == 'true'){
    // Query mein bhi exam_id ka use karein
    $delete_query = "DELETE FROM exams WHERE exam_id = $exam_id";
    if(mysqli_query($conn, $delete_query)){
        header("Location:manage_exam.php?msg=deleted");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        title: 'Kya aap sure hain?',
        text: "Record wapas nahi aayega!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Haan, Delete karein!'
    }).then((result) => {
        if (result.isConfirmed) {
            // FIX: Yahan URL mein exam_id= bhejna zaroori hai
            window.location.href = "delete-exam.php?exam_id=<?= $exam_id ?>&confirm=true";
        } else {
            window.location.href = "manage_exam.php";
        }
    });
</script>
</body>
</html>