<?php
session_start();
include "../db_connect.php";

$owner_id = $_SESSION['user_id'];

$sql = "SELECT 
    b.*, 
    u.name AS user_name,
    h.hotel_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN hotels h ON b.hotel_id = h.id
WHERE b.owner_id = $owner_id
ORDER BY b.id DESC
";

$result = mysqli_query($conn, $sql);
