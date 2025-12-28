<?php
session_start();
include "../db_connect.php";

$owner_id = $_SESSION['user_id'];
$sub_id = $_GET['id'];

// check already active subscription
$check = mysqli_query($conn,"
SELECT * FROM owner_subscriptions 
WHERE owner_id=$owner_id AND status='approved'
");

if(mysqli_num_rows($check) > 0){
    die("You already have an active subscription");
}

// get package
$sub = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM subscriptions WHERE id=$sub_id"
));

// create owner_subscription
mysqli_query($conn,"
INSERT INTO owner_subscriptions(owner_id, subscription_id, status)
VALUES($owner_id,$sub_id,'pending')
");

$owner_sub_id = mysqli_insert_id($conn);

// redirect to payment
header("Location: ../ssl/subscription_payment.php?osid=$owner_sub_id");
exit;
