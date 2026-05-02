<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$conn = mysqli_connect("localhost","root","","exam_center");

if(!$conn){
    die("Database Connection Failed");
}
?>
