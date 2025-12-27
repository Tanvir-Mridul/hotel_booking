<?php
session_start();
include "db_connect.php";

$email = $_POST['email'];
$pass  = $_POST['password'];

/* =====================
   ADMIN LOGIN
   ===================== */
if ($email === "admin@gmail.com" && $pass === "123") {
    $_SESSION['user_id'] = 0;
    $_SESSION['name']    = "Admin";
    $_SESSION['role']    = "admin";
    header("Location: admin/dashboard.php");
    exit();
}

/* =====================
   USER LOGIN
   ===================== */
$sql = "SELECT * FROM users WHERE email='$email'";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) == 1) {
    $user = mysqli_fetch_assoc($res);
    
    // Simple password check (for testing)
    if ($pass === "123456" || password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        
        if ($user['role'] == 'owner') {
            header("Location: owner/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "❌ Invalid Password";
    }
} else {
    echo "❌ Invalid Email";
}
?>