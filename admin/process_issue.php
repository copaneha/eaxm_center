<?php
include("../config.php");

// SweetAlert2 library include karne ke liye header section
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body>

<?php
if(isset($_GET['id']) && isset($_GET['date'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $date = mysqli_real_escape_string($conn, $_GET['date']);

    // Update query
    $query = "UPDATE exam_submissions SET is_issued = 1, issued_at = '$date' WHERE id = '$id'";

    if(mysqli_query($conn, $query)) {
        // Success SweetAlert
        echo "<script>
            Swal.fire({
                title: 'Result Issued!',
                text: 'The transcript has been officially published.',
                icon: 'success',
                confirmButtonColor: '#002366',
                confirmButtonText: 'Great!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='view_results.php';
                }
            });
        </script>";
    } else {
        // Error SweetAlert
        $error = mysqli_real_escape_string($conn, mysqli_error($conn));
        echo "<script>
            Swal.fire({
                title: 'Issue Failed',
                text: 'Error: $error',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
        </script>";
    }
}
?>
</body>
</html>