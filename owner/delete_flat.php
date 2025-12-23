<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$owner_id = $_SESSION['user_id'];

// Delete only if belongs to owner
$sql = "DELETE FROM hotels WHERE id='$id' AND owner_id='$owner_id'";
mysqli_query($conn, $sql);

header("Location: dashboard.php");
?>
