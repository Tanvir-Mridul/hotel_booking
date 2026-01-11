<?php
session_start();
require_once "../db_connect.php";

$sender_id   = $_SESSION['user_id'];
$sender_role = $_SESSION['role'];

$receiver_id   = $_POST['receiver_id'];
$receiver_role = $_POST['receiver_role'];
$msg = trim($_POST['message']);

if (!empty($msg)) {
    mysqli_query($conn, "
        INSERT INTO chat_messages 
        (sender_id, sender_role, receiver_id, receiver_role, message) 
        VALUES 
        ('$sender_id','$sender_role','$receiver_id','$receiver_role','$msg')
    ");
    
    // Notification for new message
    mysqli_query($conn, "
        INSERT INTO notifications (receiver_id, receiver_role, message, link)
        VALUES (
            '$receiver_id',
            '$receiver_role',
            '💬 New message from ' . (SELECT name FROM users WHERE id='$sender_id'),
            'chat/chat.php?" . ($receiver_role=='owner'?'user_id':'owner_id') . "=$sender_id'
        )
    ");
}
?>