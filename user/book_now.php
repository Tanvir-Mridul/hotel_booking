<?php
session_start();
include "../db_connect.php";

$user_id  = $_SESSION['user_id'];
$hotel_id = $_POST['hotel_id'];   // hidden input থেকে আসবে

// 1️⃣ hotel থেকে owner_id বের করো
$hotelQuery = mysqli_query(
    $conn,
    "SELECT owner_id, hotel_name, location, price 
     FROM hotels 
     WHERE id='$hotel_id'"
);

$hotel = mysqli_fetch_assoc($hotelQuery);

if (!$hotel) {
    die("Hotel not found");
}

$owner_id   = $hotel['owner_id'];
$hotel_name = $hotel['hotel_name'];
$location   = $hotel['location'];
$price      = $hotel['price'];

// 2️⃣ booking insert করো
$sql = "INSERT INTO bookings
(user_id, hotel_id, owner_id, hotel_name, location, price, booking_date, status)
VALUES
('$user_id', '$hotel_id', '$owner_id', '$hotel_name', '$location', '$price', NOW(), 'pending')";

if (!mysqli_query($conn, $sql)) {
    die("Booking failed: " . mysqli_error($conn));
}
mysqli_query($conn,"
INSERT INTO notifications (receiver_id, receiver_role, message)
VALUES ('$owner_id','owner','A user booked your hotel')
");

// 3️⃣ success
header("Location: ../user/my_booking.php?success=1");
exit;



<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>
    
    <title>Your Page Title</title>
</head>
<body>
<!-- Your page content -->
</body>
</html>