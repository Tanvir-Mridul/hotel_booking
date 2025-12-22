<?php
session_start();
include "../db_connect.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

$sql = "UPDATE hotels SET status='approved' WHERE id='$id'";
mysqli_query($conn, $sql);

header("Location: hotels.php");
exit();
