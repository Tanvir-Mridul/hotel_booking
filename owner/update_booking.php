<?php
session_start();
include "../db_connect.php";
include "../includes/notification_helper.php";

if ($_SESSION['role'] != 'owner') {
    exit("Unauthorized");
}

$id     = $_GET['id'];
$action = $_GET['action'];

// booking info নাও
$bq = mysqli_query($conn, "
    SELECT user_id, hotel_name, owner_id
    FROM bookings 
    WHERE id='$id'
");

$booking = mysqli_fetch_assoc($bq);
$user_id = $booking['user_id'];
$hotel   = $booking['hotel_name'];
$owner_id = $booking['owner_id'];

if ($action == 'confirm') {
    mysqli_query($conn, "UPDATE bookings SET status='confirmed' WHERE id='$id'");
    
    // 🔔 notify user
    sendNotification($user_id, 'user',
        "✅ Your booking for \"$hotel\" has been confirmed by owner",
        "/hotel_booking/user/my_booking.php"
    );
    
    // 🔔 notify admin
    $admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
    $admin = mysqli_fetch_assoc($admin_q);
    if ($admin) {
        sendNotification($admin['id'], 'admin',
            "✅ Booking #$id confirmed by owner for \"$hotel\"",
            "/hotel_booking/admin/dashboard.php"
        );
    }
}

if ($action == 'cancel') {
    mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id='$id'");
    
    // 🔔 notify user
    sendNotification($user_id, 'user',
        "❌ Your booking for \"$hotel\" was cancelled by owner",
        "/hotel_booking/user/my_booking.php"
    );
    
    // 🔔 notify admin
    $admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
    $admin = mysqli_fetch_assoc($admin_q);
    if ($admin) {
        sendNotification($admin['id'], 'admin',
            "❌ Booking #$id cancelled by owner for \"$hotel\"",
            "/hotel_booking/admin/dashboard.php"
        );
    }
}

header("Location: manage_bookings.php");
exit();
?>