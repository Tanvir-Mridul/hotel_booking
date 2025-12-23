<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Approve hotel
$sql = "UPDATE hotels SET status='approved' WHERE id='$id'";
mysqli_query($conn, $sql);

header("Location: hotels.php?msg=approved");
?>