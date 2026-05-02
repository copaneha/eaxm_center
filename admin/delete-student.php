<?php
include("../config.php");

$id = $_GET['id'];

// Purani photo delete karne ke liye (Optional but recommended)
$get_photo = mysqli_query($conn, "SELECT photo FROM students WHERE id=$id");
$res = mysqli_fetch_assoc($get_photo);
if($res['photo'] != "") {
    unlink("../image/".$res['photo']); 
}

// Delete query
$del = mysqli_query($conn, "DELETE FROM students WHERE id=$id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php if($del): ?>
    <script>
        Swal.fire({
            title: 'Deleted!',
            text: 'Student record has been removed.',
            icon: 'success',
            confirmButtonColor: '#3085d6'
        }).then((result) => {
            window.location.href = 'Manage-Student.php';
        });
    </script>
    <?php else: ?>
    <script>
        Swal.fire('Error!', 'Something went wrong.', 'error').then(() => {
            window.location.href = 'Manage-Student.php';
        });
    </script>
    <?php endif; ?>
</body>
</html>