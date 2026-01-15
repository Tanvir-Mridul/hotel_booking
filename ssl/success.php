//success.php//
<?php
include "../db_connect.php";
include "../includes/notification_helper.php";

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
    
    // 🔔 Notify Admin - FIXED VERSION
    $admin_q = mysqli_query($conn, "SELECT id, name FROM users WHERE role='admin' LIMIT 1");
    
    if ($admin_q && mysqli_num_rows($admin_q) > 0) {
        $admin = mysqli_fetch_assoc($admin_q);
        $admin_id = $admin['id'];
        
        // Get owner name
        $owner_q = mysqli_query($conn, "SELECT name FROM users WHERE id='$owner_id'");
        $owner = mysqli_fetch_assoc($owner_q);
        $owner_name = $owner['name'] ?? "Owner ID: $owner_id";
        
        // Get package name
        $package_name = $pkg['name'] ?? "Package ID: $package_id";
        
        sendNotification($admin_id, 'admin',
            "💳 New subscription request from $owner_name - $package_name (৳$amount)",
            "/hotel_booking/admin/manage_subscriptions.php"
        );
        
        error_log("Admin notification sent to ID: $admin_id");
    } else {
        error_log("No admin user found in database!");
    }
    
    // Also notify owner
    sendNotification($owner_id, 'owner',
        "✅ Subscription payment successful! Waiting for admin approval.",
        "/hotel_booking/owner/subscription.php"
    );
    
    header("Location: ../owner/subscription.php?success=1");
} else {
    echo "Database error! " . mysqli_error($conn);
}
?>