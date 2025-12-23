<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Reject hotel
$sql = "UPDATE hotels SET status='rejected' WHERE id='$id'";
mysqli_query($conn, $sql);

header("Location: hotels.php?msg=rejected");
?>