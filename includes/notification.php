<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "db_connect.php";

/* ===== FETCH NOTIFICATIONS ===== */
$notifications = [];

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    $uid = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    $noti_q = mysqli_query($conn, "
        SELECT * FROM notifications 
        WHERE receiver_id='$uid' 
        AND receiver_role='$role'
        ORDER BY id DESC
        LIMIT 5
    ");

    while ($row = mysqli_fetch_assoc($noti_q)) {
        $notifications[] = $row;
    }
}
?>
