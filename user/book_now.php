<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get booking data
$hotel_id = $_POST['hotel_id'];
$hotel_name = $_POST['hotel_name'];
$location = $_POST['location'];
$price = $_POST['price'];
$booking_date = $_POST['booking_date'];

// Insert booking
$sql = "INSERT INTO bookings (user_id, hotel_name, location, price, booking_date, status) 
        VALUES ('$user_id', '$hotel_name', '$location', '$price', '$booking_date', 'pending')";

if(mysqli_query($conn, $sql)) {
    header("Location: ../user/my_booking.php?msg=booked");
} else {
    echo "Booking error: " . mysqli_error($conn);
}
?>