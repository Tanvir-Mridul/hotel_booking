<?php
session_start();
include "../db_connect.php";

// User login check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['hotel_id']) || !isset($_POST['booking_date'])) {
    header("Location: hotel_list.php");
    exit();
}

$hotel_id = intval($_POST['hotel_id']);
$booking_date = $_POST['booking_date'];

// Validate date
if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
    showErrorWithButton("❌ Cannot book past dates!", $hotel_id);
}

// Get hotel info
$hotel_q = $conn->prepare("
    SELECT id, hotel_name, location, price, owner_id, booked_dates 
    FROM hotels 
    WHERE id = ? AND status = 'approved'
");
$hotel_q->bind_param("i", $hotel_id);
$hotel_q->execute();
$hotel = $hotel_q->get_result()->fetch_assoc();

if (!$hotel) {
    showErrorWithButton("Invalid hotel!", $hotel_id);
}

// Check if date is already booked
$booked_dates = json_decode($hotel['booked_dates'] ?? '[]', true);
if (in_array($booking_date, $booked_dates)) {
    showErrorWithButton("❌ This date is already booked! Please choose another date.", $hotel_id);
}

// Insert booking
$insert = $conn->prepare("INSERT INTO bookings 
    (user_id, owner_id, hotel_id, hotel_name, location, price, booking_date, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
");

$insert->bind_param(
    "iiissds",
    $user_id,
    $hotel['owner_id'],
    $hotel_id,
    $hotel['hotel_name'],
    $hotel['location'],
    $hotel['price'],
    $booking_date
);

if ($insert->execute()) {
    $booking_id = $insert->insert_id;
    
    // Add date to booked dates
    $booked_dates[] = $booking_date;
    $new_booked_dates = json_encode(array_unique($booked_dates));
    
    $update_q = $conn->prepare("UPDATE hotels SET booked_dates = ? WHERE id = ?");
    $update_q->bind_param("si", $new_booked_dates, $hotel_id);
    $update_q->execute();
    
    // Redirect to payment
    header("Location: ../user/payment_checkout.php?booking_id=" . $booking_id);
    exit();
} else {
    showErrorWithButton("Booking failed!", $hotel_id);
}

// Function to show error with back button
function showErrorWithButton($error_message, $hotel_id) {
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
        <a href="hotel_details.php?id=<?php echo $hotel_id; ?>" class="back-button">
            ← Back to Hotel
        </a>
    </div>
</body>
</html>
    <?php
    exit();
}
?>