<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$current = $_GET['current'];

// Toggle status
if($current == 'approved') {
    $new_status = 'off';
} else {
    $new_status = 'approved';
}

$sql = "UPDATE hotels SET status='$new_status' WHERE id='$id'";
mysqli_query($conn, $sql);

header("Location: hotels.php?msg=toggled");
?>