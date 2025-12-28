<?php
include "../db_connect.php";
$id = $_GET['id'];

mysqli_query($conn,
"UPDATE owner_subscriptions SET status='approved' WHERE id=$id");

header("Location: manage_subscriptions.php");
