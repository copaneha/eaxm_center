<?php
include("../config.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security Check: Sirf admin ya authorized person hi delete kar sake
// if(!isset($_SESSION['admin_id'])) { die("Unauthorized!"); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body>

<?php
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Professional Delete Logic with SweetAlert Confirmation
    // Note: Database se delete karne se pehle confirmation zaroori hai
    
    echo "<script>
        Swal.fire({
            title: 'Are you sure?',
            text: 'This record will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Agar user 'Yes' kare tabhi PHP delete execute kare
                window.location.href = 'delete_result.php?confirm_id=$id';
            } else {
                window.location.href = 'view_results.php';
            }
        });
    </script>";
}

// Actual Deletion Process after Confirmation
if(isset($_GET['confirm_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['confirm_id']);
    
    $query = "DELETE FROM exam_submissions WHERE id = '$delete_id'";

    if(mysqli_query($conn, $query)) {
        echo "<script>
            Swal.fire({
                title: 'Deleted!',
                text: 'The result record has been removed.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'view_results.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Could not delete record: " . mysqli_error($conn) . "',
                icon: 'error'
            });
        </script>";
    }
}
?>
</body>
</html>