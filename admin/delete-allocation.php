<?php
include("../config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; }</style>
</head>
<body>

<?php
if (isset($_GET['id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Seat allocation table se record delete karein
    $delete = mysqli_query($conn, "DELETE FROM seat_allocation WHERE id = '$id'");

    if ($delete) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'The allocation has been removed successfully.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'manage-allocation.php';
            });
        </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: manage-allocation.php");
    exit();
}
?>
</body>
</html>