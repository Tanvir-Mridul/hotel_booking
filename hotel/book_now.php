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
mysqli_query($conn,"INSERT INTO notifications (receiver_id, receiver_role, message, link)
VALUES (
    '$owner_id','owner',
    'A user booked your hotel',
    'owner/manage_bookings.php'
)
");


if (!mysqli_query($conn, $sql)) {
    die("Booking failed: " . mysqli_error($conn));
}

// 3️⃣ success
header("Location: ../user/my_booking.php?success=1");
exit;
