<?php
session_start();
include "../db_connect.php";

// User login check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['room_id']) || !isset($_POST['check_in']) || !isset($_POST['check_out'])) {
    header("Location: room_details.php?room_id=" . ($_POST['room_id'] ?? 0));
    exit();
}

$room_id = intval($_POST['room_id']);
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$rooms_count = intval($_POST['rooms_count'] ?? 1);
$guests = intval($_POST['guests'] ?? 1);

// Validate dates
if (strtotime($check_in) < strtotime(date('Y-m-d'))) {
    showErrorWithButton("❌ Cannot book past dates!", $room_id);
}

if (strtotime($check_out) <= strtotime($check_in)) {
    showErrorWithButton("❌ Check-out date must be after check-in date!", $room_id);
}

// Get room info - FIXED: Removed r.status condition
$room_q = $conn->prepare("
    SELECT r.*, h.hotel_name, h.location, h.owner_id 
    FROM rooms r 
    JOIN hotels h ON r.hotel_id = h.id 
    WHERE r.id = ?
");
$room_q->bind_param("i", $room_id);
$room_q->execute();
$room = $room_q->get_result()->fetch_assoc();

if (!$room) {
    showErrorWithButton("Room not found!", $room_id);
}

// Check if room is active
if (isset($room['active']) && $room['active'] != 1) {
    showErrorWithButton("❌ Room is not available for booking!", $room_id);
}

// Check capacity
if ($guests > $room['capacity']) {
    showErrorWithButton("❌ Maximum capacity for this room is {$room['capacity']} persons!", $room_id);
}

if ($rooms_count > $room['room_count']) {
    showErrorWithButton("❌ Only {$room['room_count']} room(s) available!", $room_id);
}

// ========== CHECK DATE AVAILABILITY ==========
$all_dates_available = true;
$conflict_dates = [];

// Create date range
$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($check_in_date, $interval, $check_out_date);

foreach ($period as $dt) {
    $current_date = $dt->format("Y-m-d");
    
    // 1. Check if date is already booked
    $booking_check_sql = "SELECT id FROM bookings 
                         WHERE room_id = ? 
                         AND status IN ('pending', 'confirmed')
                         AND ? BETWEEN check_in_date AND DATE_SUB(check_out_date, INTERVAL 1 DAY)";
    
    $booking_check_stmt = $conn->prepare($booking_check_sql);
    $booking_check_stmt->bind_param("is", $room_id, $current_date);
    $booking_check_stmt->execute();
    $booking_check_result = $booking_check_stmt->get_result();
    
    if ($booking_check_result->num_rows > 0) {
        // Already booked
        $all_dates_available = false;
        $conflict_dates[] = [
            'date' => $current_date,
            'reason' => 'already booked'
        ];
        continue; // Skip blocked check if already booked
    }
    
    // 2. Check if date is blocked by owner
    // First check if entire hotel is blocked
    $hotel_block_sql = "SELECT id FROM blocked_dates 
                       WHERE hotel_id = ? 
                       AND room_id = 0
                       AND blocked_date = ?";
    
    $hotel_block_stmt = $conn->prepare($hotel_block_sql);
    $hotel_block_stmt->bind_param("is", $room['hotel_id'], $current_date);
    $hotel_block_stmt->execute();
    $hotel_block_result = $hotel_block_stmt->get_result();
    
    if ($hotel_block_result->num_rows > 0) {
        // Entire hotel blocked
        $all_dates_available = false;
        $conflict_dates[] = [
            'date' => $current_date,
            'reason' => 'hotel blocked by owner'
        ];
        continue;
    }
    
    // Check if specific room is blocked
    $room_block_sql = "SELECT id FROM blocked_dates 
                      WHERE hotel_id = ? 
                      AND room_id = ?
                      AND blocked_date = ?";
    
    $room_block_stmt = $conn->prepare($room_block_sql);
    $room_block_stmt->bind_param("iis", $room['hotel_id'], $room_id, $current_date);
    $room_block_stmt->execute();
    $room_block_result = $room_block_stmt->get_result();
    
    if ($room_block_result->num_rows > 0) {
        // Specific room blocked
        $all_dates_available = false;
        $conflict_dates[] = [
            'date' => $current_date,
            'reason' => 'room booked by owner'
        ];
    }
}

if (!$all_dates_available) {
    // Remove duplicate conflicts
    $conflict_dates = array_unique($conflict_dates, SORT_REGULAR);
    
    $error_message = "❌ The following dates are not available:\n";
    foreach ($conflict_dates as $conflict) {
        $formatted_date = date('d M Y', strtotime($conflict['date']));
        $error_message .= "- {$formatted_date} ({$conflict['reason']})\n";
    }
    $error_message .= "\nPlease choose different dates.";
    showErrorWithButton($error_message, $room_id);
}
// ========== END DATE CHECK ==========

// Calculate total price
$nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
$nights = max(1, $nights);
$total_price = $nights * $room['price_per_night'] * $rooms_count;

// Insert booking
$insert = $conn->prepare("INSERT INTO bookings 
    (user_id, hotel_id, room_id, room_title, hotel_name, location, price, 
     check_in_date, check_out_date, rooms_count, guests, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

$insert->bind_param(
    "iiisssdssii",
    $user_id,
    $room['hotel_id'],
    $room_id,
    $room['room_title'],
    $room['hotel_name'],
    $room['location'],
    $total_price,
    $check_in,
    $check_out,
    $rooms_count,
    $guests
);

if ($insert->execute()) {
    $booking_id = $insert->insert_id;
    
    // Send notification to owner
    include "../includes/notification_helper.php";
    sendNotification($room['owner_id'], 'owner',
        "📅 New booking request for \"{$room['room_title']}\" from " . $_SESSION['name'],
        "/hotel_booking/owner/manage_bookings.php"
    );
    
    // Also notify user
    sendNotification($user_id, 'user',
        "✅ Booking request sent for \"{$room['room_title']}\". Please complete payment.",
        "/hotel_booking/user/my_booking.php"
    );
    
    // Redirect to payment
    header("Location: ../user/payment_checkout.php?booking_id=" . $booking_id);
    exit();
} else {
    error_log("Booking insert failed: " . $insert->error);
    showErrorWithButton("Booking failed! Error: " . $insert->error, $room_id);
}

// Function to show error with back button
function showErrorWithButton($error_message, $room_id) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                padding: 40px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                max-width: 500px;
                width: 90%;
            }
            .error-icon {
                font-size: 60px;
                color: #dc3545;
                margin-bottom: 20px;
            }
            .error-message {
                color: #dc3545;
                font-size: 18px;
                margin-bottom: 25px;
                line-height: 1.5;
                white-space: pre-line;
            }
            .back-button {
                display: inline-block;
                background-color: #007bff;
                color: white;
                padding: 12px 30px;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            .back-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <div class="error-message"><?php echo $error_message; ?></div>
            <a href="room_details.php?room_id=<?php echo $room_id; ?>" class="back-button">
                ← Back to Room
            </a>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>