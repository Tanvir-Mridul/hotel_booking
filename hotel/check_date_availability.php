<?php
session_start();
include "../db_connect.php";

$hotel_id = $_POST['hotel_id'] ?? 0;
$date = $_POST['date'] ?? '';

if (!$hotel_id || !$date) {
    echo "invalid";
    exit();
}

// Get hotel's booked dates
$sql = "SELECT booked_dates FROM hotels WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $booked_dates = json_decode($row['booked_dates'] ?? '[]', true);
    
    if (in_array($date, $booked_dates)) {
        echo "booked";
    } else {
        echo "available";
    }
} else {
    echo "error";
}

$conn->close();
?>