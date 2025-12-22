<?php
include "db_connect.php";

$name  = $_POST['name'];
$email = $_POST['email'];
$pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role  = $_POST['role'];

$sql = "INSERT INTO users (name, email, password, role)
        VALUES ('$name', '$email', '$pass', '$role')";

mysqli_query($conn, $sql);

header("Location: login.php");
exit();
