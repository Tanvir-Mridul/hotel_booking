<?php
session_start();
$hotel_id = $_GET['hotel_id'];
$user_id  = $_SESSION['user_id'];

// hotel থেকে owner বের করো
$hotelQuery = mysqli_query($conn, "SELECT owner_id, price FROM hotels WHERE id='$hotel_id'");
$hotel = mysqli_fetch_assoc($hotelQuery);

$owner_id = $hotel['owner_id'];
$price    = $hotel['price'];

$sql = "INSERT INTO bookings 
        (user_id, hotel_id, owner_id, price, booking_date, status)
        VALUES 
        ('$user_id', '$hotel_id', '$owner_id', '$price', NOW(), 'pending')";

mysqli_query($conn, $sql);

header("Location: my_booking.php");
?>





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