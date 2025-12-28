<?php
session_start();
include "../db_connect.php";

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM bookings WHERE id='$id'");

header("Location: manage_bookings.php");
