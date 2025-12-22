<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔐 Escape all string inputs
$hotel_name   = mysqli_real_escape_string($conn, $_POST['hotel_name']);
$location     = mysqli_real_escape_string($conn, $_POST['location']);
$price        = $_POST['price'];
$booking_date = $_POST['booking_date'];

$sql = "INSERT INTO bookings (user_id, hotel_name, location, price, booking_date)
        VALUES ('$user_id', '$hotel_name', '$location', '$price', '$booking_date')";

mysqli_query($conn, $sql);

header("Location: ../user/my_booking.php");
exit();
