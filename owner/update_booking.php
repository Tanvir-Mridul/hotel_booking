<?php
session_start();
include "../db_connect.php";

if ($_SESSION['role'] != 'owner') {
    exit("Unauthorized");
}

$id     = $_GET['id'];
$action = $_GET['action'];

// booking info নাও
$bq = mysqli_query($conn,"
SELECT user_id, hotel_name 
FROM bookings 
WHERE id='$id'
");

$booking = mysqli_fetch_assoc($bq);
$user_id = $booking['user_id'];
$hotel   = $booking['hotel_name'];

if ($action == 'confirm') {
    mysqli_query($conn,"
    UPDATE bookings SET status='confirmed' WHERE id='$id'
    ");

    // 🔔 notify user
    mysqli_query($conn,"INSERT INTO notifications (receiver_id, receiver_role, message, link)
    VALUES (
        '$user_id',
        'user',
        'Your booking for $hotel has been confirmed',
        'user/my_booking.php'
    )
    ");
}

if ($action == 'cancel') {
    mysqli_query($conn,"
    UPDATE bookings SET status='cancelled' WHERE id='$id'
    ");

    // 🔔 notify user
    mysqli_query($conn,"INSERT INTO notifications (receiver_id, receiver_role, message, link)
    VALUES (
        '$user_id',
        'user',
        'Your booking for $hotel was cancelled',
        'user/my_booking.php'
    )
    ");
}

header("Location: manage_bookings.php");
exit;
