<?php
session_start();
include "../db_connect.php";
include "../includes/notification_helper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Get hotel info before update
$hotel_q = mysqli_query($conn, "SELECT hotel_name, owner_id FROM hotels WHERE id='$id'");
$hotel = mysqli_fetch_assoc($hotel_q);

// Reject hotel
$sql = "UPDATE hotels SET status='rejected' WHERE id='$id'";
mysqli_query($conn, $sql);

// 🔔 Notify Owner
sendNotification($hotel['owner_id'], 'owner',
    "❌ Your hotel \"{$hotel['hotel_name']}\" has been rejected by admin.",
    "/hotel_booking/owner/dashboard.php"
);

header("Location: hotels.php?msg=rejected");
?>