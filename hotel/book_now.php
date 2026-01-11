<?php
session_start();
include "../db_connect.php";

// User login check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id  = $_SESSION['user_id'];

if (!isset($_POST['hotel_id'])) {
    header("Location: hotel_list.php");
    exit();
}

$hotel_id = intval($_POST['hotel_id']);

/* ===============================
   Get hotel info
================================ */
$hotel_q = $conn->prepare("
    SELECT id, hotel_name, location, price, owner_id 
    FROM hotels 
    WHERE id = ? AND status = 'approved'
");
$hotel_q->bind_param("i", $hotel_id);
$hotel_q->execute();
$hotel = $hotel_q->get_result()->fetch_assoc();

if (!$hotel) {
    die("Invalid hotel!");
}

$hotel_name = $hotel['hotel_name'];
$location   = $hotel['location'];
$price      = $hotel['price'];
$owner_id   = $hotel['owner_id'];

/* ===============================
   Insert booking (SAFE)
================================ */
$insert = $conn->prepare("INSERT INTO bookings 
    (user_id, owner_id, hotel_id, hotel_name, location, price, booking_date, status)
    VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')
");

$insert->bind_param(
    "iiissd",
    $user_id,
    $owner_id,
    $hotel_id,
    $hotel_name,
    $location,
    $price
);

$insert->execute();
$booking_id = $insert->insert_id;

/* ===============================
   Include notification helper
================================ */
include "../includes/notification_helper.php";

/* ===============================
   Owner notification
================================ */
sendNotification($owner_id, 'owner', 
    "📅 New booking request for \"$hotel_name\" from " . $_SESSION['name'],
    "/hotel_booking/owner/manage_bookings.php"
);

/* ===============================
   Admin notification
================================ */
// Find admin user (assuming admin id is 5)
$admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
$admin = mysqli_fetch_assoc($admin_q);
if ($admin) {
    sendNotification($admin['id'], 'admin',
        "📅 New booking created for \"$hotel_name\"",
        "/hotel_booking/admin/dashboard.php"
    );
}

/* ===============================
   Redirect user
================================ */
header("Location: ../user/my_booking.php?success=1");
exit();
?>