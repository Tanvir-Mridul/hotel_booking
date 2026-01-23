<?php
session_start();
error_log("=== SSL SUCCESS DEBUG VERSION ===");

include "../db_connect.php";
include "../includes/notification_helper.php";

//  সব POST data log
error_log("POST Data: " . print_r($_POST, true));
error_log("SESSION Data: " . print_r($_SESSION, true));

//  SSL response validate 
if (!isset($_POST['status'])) {
    die("No payment status received");
}

if ($_POST['status'] != "VALID") {
    error_log("Payment failed. Status: " . $_POST['status']);
    header("Location: ../user/payment_failed.php");
    exit();
}

//  Extract data
$tran_id = $_POST['tran_id'] ?? '';
$amount = $_POST['amount'] ?? 0;
$booking_id = $_POST['value_a'] ?? 0; // booking_id
$user_id = $_POST['value_b'] ?? 0; // user_id

error_log("Extracted: TranID=$tran_id, Amount=$amount, Booking=$booking_id, User=$user_id");

// Check if already processed
$check_sql = "SELECT id, receipt_id FROM user_payments WHERE tran_id='$tran_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    $payment = mysqli_fetch_assoc($check_result);
    error_log("Duplicate transaction found. Redirecting to receipt: " . $payment['receipt_id']);
    header("Location: ../user/receipt.php?receipt_id=" . $payment['receipt_id']);
    exit();
}

//  Get booking details
$booking_q = mysqli_query($conn, "
    SELECT b.*, 
           u.name as user_name, 
           h.hotel_name, 
           h.owner_id,
           r.room_title
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    LEFT JOIN rooms r ON b.room_id = r.id
    WHERE b.id = '$booking_id'
");

if (!$booking_q) {
    error_log("Booking query failed: " . mysqli_error($conn));
    die("Booking query failed");
}

if (mysqli_num_rows($booking_q) == 0) {
    error_log("No booking found with ID: $booking_id");
    die("Booking not found in database");
}

$booking = mysqli_fetch_assoc($booking_q);
error_log("Booking found: " . print_r($booking, true));

$hotel_name = $booking['hotel_name'] ?? 'Unknown Hotel';
$user_name = $booking['user_name'] ?? 'Customer';
$owner_id = $booking['owner_id'] ?? 0;
$room_title = $booking['room_title'] ?? NULL;

//  Generate receipt ID
$receipt_id = "REC" . date('YmdHis') . rand(100, 999);
error_log("Generated Receipt ID: $receipt_id");

// Calculate amounts - User pays FULL amount
$full_amount = $amount; // User pays full amount
$commission = $full_amount * 0.10; // Admin keeps 10%
$owner_amount = $full_amount - $commission; // Owner gets 90%

error_log("User paid: $full_amount | Commission: $commission | Owner gets: $owner_amount");

// Insert into user_payments
$insert_sql = "INSERT INTO user_payments (
    user_id, 
    owner_id, 
    booking_id, 
    hotel_name, 
    room_title,
    amount, 
    commission, 
    owner_amount, 
    tran_id, 
    payment_status, 
    admin_status, 
    receipt_id, 
    booking_date
) VALUES (
    '$user_id',
    '$owner_id',
    '$booking_id',
    '$hotel_name',
    " . ($room_title ? "'$room_title'" : "NULL") . ",
    '$full_amount',
    '$commission',
    '$owner_amount',
    '$tran_id',
    'success',
    'pending',
    '$receipt_id',
    NOW()
)";

error_log("Executing SQL: $insert_sql");

if (mysqli_query($conn, $insert_sql)) {
    $payment_id = mysqli_insert_id($conn);
    error_log("Payment inserted successfully. Payment ID: $payment_id");
} else {
    $error_msg = "Payment insert failed: " . mysqli_error($conn);
    error_log($error_msg);
    die($error_msg);
}

// Update booking status
$update_booking = mysqli_query($conn, "
    UPDATE bookings 
    SET status='confirmed', payment_status='success'
    WHERE id='$booking_id'
");
if ($update_booking) {
    error_log("Booking status updated to confirmed");
} else {
    error_log("Booking update failed: " . mysqli_error($conn));
}

// 
if (!empty($booking['room_id'])) {
    // Check if you have a booked_dates or room_dates table
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'booked_dates'");
    if (mysqli_num_rows($check_table) > 0) {
        // Insert booking dates into booked_dates
        $check_in = $booking['check_in_date'];
        $check_out = $booking['check_out_date'];

        $date_insert = "INSERT INTO booked_dates (room_id, booking_id, date) 
                        SELECT '{$booking['room_id']}', '$booking_id', dates.date
                        FROM (
                            SELECT DATE_ADD('$check_in', INTERVAL t4.i*1000 + t3.i*100 + t2.i*10 + t1.i DAY) as date
                            FROM (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                                 (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                                 (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                                 (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
                            WHERE DATE_ADD('$check_in', INTERVAL t4.i*1000 + t3.i*100 + t2.i*10 + t1.i DAY) < '$check_out'
                        ) dates";

        if (mysqli_query($conn, $date_insert)) {
            error_log("Booking dates inserted into booked_dates");
        } else {
            error_log("Failed to insert booking dates: " . mysqli_error($conn));
        }
    }
}

//  Insert commission into admin_commissions
$commission_sql = "INSERT INTO admin_commissions (
    payment_id, 
    user_id, 
    owner_id, 
    amount, 
    commission, 
    owner_get, 
    created_at
) VALUES (
    '$payment_id',
    '$user_id',
    '$owner_id',
    '$full_amount',
    '$commission',
    '$owner_amount',
    NOW()
)";

if (mysqli_query($conn, $commission_sql)) {
    error_log("Commission inserted");
} else {
    error_log("Commission insert failed: " . mysqli_error($conn));
}

//  Set session
$_SESSION['user_id'] = $user_id;
$_SESSION['name'] = $user_name;
$_SESSION['role'] = 'user';
$_SESSION['payment_receipt'] = $receipt_id;

error_log("Session set: UserID=$user_id, Receipt=$receipt_id");

//  Send notification to user
try {
    sendNotification(
        $user_id,
        'user',
        "✅ Payment Successful!\nReceipt ID: $receipt_id\nAmount: ৳$full_amount",
        "/hotel_booking/user/my_booking.php"
    );
    error_log("User notification sent");
} catch (Exception $e) {
    error_log("Notification error: " . $e->getMessage());
}

//  Send notification to owner
try {
    sendNotification(
        $owner_id,
        'owner',
        "✅ Payment Received!\nHotel: $hotel_name\nAmount: ৳$full_amount (You get: ৳$owner_amount)",
        "/hotel_booking/owner/finance.php"
    );
    error_log("Owner notification sent");
} catch (Exception $e) {
    error_log("Owner notification error: " . $e->getMessage());
}

//  Send notification to admin (any admin user)
try {
    $admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
    if ($admin_q && mysqli_num_rows($admin_q) > 0) {
        $admin = mysqli_fetch_assoc($admin_q);
        sendNotification(
            $admin['id'],
            'admin',
            "💰 Payment + Commission!\nAmount: ৳$full_amount\nCommission: ৳$commission\nOwner gets: ৳$owner_amount",
            "/hotel_booking/admin/manage_payments.php"
        );
        error_log("Admin notification sent");
    }
} catch (Exception $e) {
    error_log("Admin notification error: " . $e->getMessage());
}

//  Redirect to receipt
$redirect_url = "/hotel_booking/user/receipt.php?receipt_id=" . urlencode($receipt_id);
error_log("Redirecting to: $redirect_url");
header("Location: $redirect_url");
exit();