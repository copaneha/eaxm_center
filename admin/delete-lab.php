<?php
include "../config.php";
$id = $_GET['id'];
mysqli_query($conn,"DELETE FROM labs WHERE id=$id");
header("Location:manage-labs.php");