<?php
include("../config.php");

// ID check karna
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("location:view_questions.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Delete Query execute karna
$delete = mysqli_query($conn, "DELETE FROM question_bank WHERE id=$id");

if ($delete) {
    // Agar delete ho gaya toh success message
    echo "
    <script>
        Swal.fire({
            title: 'Deleted!',
            text: 'Sawal kamyabi se delete ho gaya.',
            icon: 'success',
            confirmButtonColor: '#2c5ba9'
        }).then((result) => {
            window.location.href = 'view_questions.php';
        });
    </script>";
} else {
    // Agar koi error aaye
    echo "
    <script>
        Swal.fire({
            title: 'Error!',
            text: 'Kuch galat hua. Phir se koshish karein.',
            icon: 'error'
        }).then((result) => {
            window.location.href = 'view_questions.php';
        });
    </script>";
}
?>

</body>
</html>