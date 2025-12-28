<?php
session_start();
include "../db_connect.php";

/* ✅ Admin protection */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ✅ Validate ID */
if (!isset($_GET['id'])) {
    header("Location: manage_bookings.php");
    exit();
}

$booking_id = (int) $_GET['id'];

/* ✅ Delete booking */
mysqli_query(
    $conn,
    "DELETE FROM bookings WHERE id = $booking_id"
);

/* ✅ Redirect back */
header("Location: bookings.php?msg=deleted");
exit();
