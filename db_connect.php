<?php
session_start();
$conn = mysqli_connect("localhost","root","","hotel_booking");




$host = "localhost";
$user = "root";        // XAMPP default
$pass = "";            // XAMPP default (empty)
$db   = "hotel_booking";   // Your database name

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>

