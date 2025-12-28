<?php
session_start();
include "../db_connect.php";

$owner_id = $_SESSION['user_id'];
$package_id = $_GET['id'];

// Check again to prevent multiple subscriptions
$check_sql = "SELECT * FROM owner_subscriptions 
              WHERE owner_id='$owner_id' 
              AND status IN ('pending','approved')";
$check_result = mysqli_query($conn, $check_sql);

if(mysqli_num_rows($check_result) > 0){
    echo "<script>alert('You already have an active subscription!'); window.location='subscription.php';</script>";
    exit();
}

// Insert new subscription request
$today = date("Y-m-d");
$sql = "INSERT INTO owner_subscriptions (owner_id, subscription_id, start_date, end_date, status)
        VALUES ('$owner_id', '$package_id', '$today', '$today', 'pending')";
mysqli_query($conn, $sql);

echo "<script>alert('Subscription request sent. Wait for admin approval.'); window.location='subscription.php';</script>";
