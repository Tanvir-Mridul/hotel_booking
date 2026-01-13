<?php
// DEBUG: Start session FIRST
session_start();

// Debug log
error_log("=== SSL SUCCESS CALLBACK START ===");
error_log("Session ID: " . session_id());
error_log("POST Data: " . print_r($_POST, true));
error_log("SESSION Data: " . print_r($_SESSION, true));

include "../db_connect.php";
include "../includes/notification_helper.php";

// Verify SSLCommerz response
if (!isset($_POST['status']) || $_POST['status'] != "VALID") {
    error_log("Payment status invalid: " . ($_POST['status'] ?? 'NO STATUS'));
    die("Payment verification failed");
}

$tran_id    = $_POST['tran_id'];
$amount     = $_POST['amount'];
$booking_id = $_POST['value_a'] ?? 0;
$user_id    = $_POST['value_b'] ?? 0;

error_log("Processing payment: TranID=$tran_id, Amount=$amount, Booking=$booking_id, User=$user_id");

// Check if already processed
$check_sql = "SELECT id FROM user_payments WHERE tran_id='$tran_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    error_log("Duplicate transaction: $tran_id");
    // Still redirect to receipt
    $receipt_q = mysqli_query($conn, "SELECT receipt_id FROM user_payments WHERE tran_id='$tran_id' LIMIT 1");
    $receipt = mysqli_fetch_assoc($receipt_q);
    if ($receipt) {
        header("Location: /hotel_booking/user/receipt.php?receipt_id=" . $receipt['receipt_id']);
        exit();
    }
}

// Get booking details
$booking_q = mysqli_query($conn, "
    SELECT b.*, u.name as user_name, h.owner_id, h.hotel_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    WHERE b.id = '$booking_id'
");
$booking = mysqli_fetch_assoc($booking_q);

if (!$booking) {
    error_log("Booking not found: $booking_id");
    die("Booking not found!");
}

$owner_id = $booking['owner_id'];
$hotel_name = $booking['hotel_name'];
$user_name = $booking['user_name'];

error_log("Found booking: Hotel=$hotel_name, Owner=$owner_id, UserName=$user_name");

// Calculate commission
$commission = $amount * 0.10;
$owner_amount = $amount - $commission;
$receipt_id = "RECEIPT_" . date('Ymd') . "_" . rand(1000, 9999);

error_log("Commission: $commission, Owner gets: $owner_amount, Receipt: $receipt_id");

// Insert payment
$insert_sql = "INSERT INTO user_payments 
    (user_id, owner_id, booking_id, hotel_name, amount, 
     commission, owner_amount, tran_id, payment_status, 
     admin_status, receipt_id) 
    VALUES 
    ('$user_id', '$owner_id', '$booking_id', '$hotel_name', '$amount',
     '$commission', '$owner_amount', '$tran_id', 'success', 
     'pending', '$receipt_id')";

if (!mysqli_query($conn, $insert_sql)) {
    error_log("Payment insert failed: " . mysqli_error($conn));
    die("Payment processing error");
}

$payment_id = mysqli_insert_id($conn);
error_log("Payment inserted with ID: $payment_id");

// Get booking date from bookings table
$booking_date_q = mysqli_query($conn, "SELECT booking_date FROM bookings WHERE id='$booking_id'");
$booking_date_row = mysqli_fetch_assoc($booking_date_q);
$booking_date = $booking_date_row['booking_date'] ?? date('Y-m-d');

// Update user_payments with booking_date
mysqli_query($conn, "UPDATE user_payments SET booking_date='$booking_date' WHERE id='$payment_id'");
// Update booking
mysqli_query($conn, "UPDATE bookings SET status='confirmed' WHERE id='$booking_id'");

// Insert commission
mysqli_query($conn, "
    INSERT INTO admin_commissions 
    (payment_id, user_id, owner_id, amount, commission, owner_get)
    VALUES 
    ('$payment_id', '$user_id', '$owner_id', '$amount', '$commission', '$owner_amount')
");

// Send notifications
$admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
if ($admin_q && mysqli_num_rows($admin_q) > 0) {
    $admin = mysqli_fetch_assoc($admin_q);
    
    sendNotification($admin['id'], 'admin',
        "💰 Payment + Commission!\nAmount: ৳$amount\nCommission: ৳$commission\nOwner gets: ৳$owner_amount",
        "/hotel_booking/admin/manage_payments.php"
    );
}

sendNotification($owner_id, 'owner',
    "✅ Payment Received!\nHotel: $hotel_name\nAmount: ৳$amount (You get: ৳$owner_amount)",
    "/hotel_booking/owner/finance.php"
);

sendNotification($user_id, 'user',
    "✅ Payment Successful!\nReceipt ID: $receipt_id\nAmount: ৳$amount",
    "/hotel_booking/user/my_booking.php"
);

error_log("Notifications sent. Redirecting to receipt...");

// IMPORTANT: Set session variables AGAIN to ensure they're preserved
$_SESSION['user_id'] = $user_id;
$_SESSION['name'] = $user_name;
$_SESSION['role'] = 'user';
$_SESSION['payment_receipt'] = $receipt_id;

error_log("Session refreshed: UserID=" . $_SESSION['user_id'] . ", Name=" . $_SESSION['name']);

// Redirect to receipt WITH session
$redirect_url = "/hotel_booking/user/receipt.php?receipt_id=" . urlencode($receipt_id);
error_log("Redirecting to: $redirect_url");

header("Location: $redirect_url");
exit();
?>