<?php
// mark_notification_read.php
session_start();
include "db_connect.php";

if (isset($_POST['notification_id'])) {
    $noti_id = (int)$_POST['notification_id'];
    $uid = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    mysqli_query($conn, "
        UPDATE notifications 
        SET status='read' 
        WHERE id='$noti_id' 
        AND receiver_id='$uid' 
        AND receiver_role='$role'
    ");
    
    echo "OK";
}
?>