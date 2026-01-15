<?php
// Error reporting on
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";        
$pass = "";            
$db   = "hotel_booking";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>