<?php 
include "../config.php"; 
$id = $_GET['id'];

// Pehle check karein ki ID valid hai
if(isset($id) && is_numeric($id)) {
    $query = mysqli_query($conn, "DELETE FROM exam_centres WHERE id=$id");
    
    if($query) {
        // Success hone par 'msg=deleted' bhejein
        header("Location: manage-centers.php?msg=deleted");
    } else {
        // Error hone par 'msg=error' bhejein
        header("Location: manage-centers.php?msg=error");
    }
} else {
    header("Location: manage-centers.php");
}
exit();
?>