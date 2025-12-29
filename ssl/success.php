<?php
include "../db_connect.php";

// SSLCommerz validation
if (!isset($_POST['status']) || $_POST['status'] != "VALID") {
    die("Payment not valid");
}

$tran_id    = $_POST['tran_id'];
$amount     = $_POST['amount'];
$package_id = $_POST['value_a']; // package_id
$owner_id   = $_POST['value_b']; // owner_id

$pay_time = date("Y-m-d H:i:s");

// get package info
$pkg_q = mysqli_query($conn,"SELECT * FROM subscriptions WHERE id='$package_id'");
$pkg = mysqli_fetch_assoc($pkg_q);

$duration = $pkg['duration_days'];
$expiry   = date('Y-m-d', strtotime("+$duration days"));

// insert subscription
$sql = "INSERT INTO owner_subscriptions
        (owner_id, package_id, tran_id, amount, start_date, end_date, status)
        VALUES
        ('$owner_id','$package_id','$tran_id','$amount','$pay_time','$expiry','pending')";

if(mysqli_query($conn,$sql)){
    header("Location: ../owner/subscription.php?success=1");
} else {
    echo "Database error!";
}
