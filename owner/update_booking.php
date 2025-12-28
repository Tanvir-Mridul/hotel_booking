<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$booking_id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'confirm') {
    $status = 'confirmed';
} elseif ($action == 'cancel') {
    $status = 'cancelled';
} else {
    header("Location: manage_bookings.php");
    exit();
}

mysqli_query($conn, "
    UPDATE bookings 
    SET status='$status' 
    WHERE id='$booking_id' AND owner_id='$owner_id'
");

header("Location: manage_bookings.php");
exit();
