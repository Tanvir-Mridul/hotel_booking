<?php
session_start();
require_once "../db_connect.php";

$uid   = $_SESSION['user_id'];
$role  = $_SESSION['role'];
$rid   = $_GET['rid'];
$rrole = $_GET['rrole'];

$q = mysqli_query($conn, "
    SELECT * FROM chat_messages
    WHERE 
    (sender_id='$uid' AND receiver_id='$rid')
    OR
    (sender_id='$rid' AND receiver_id='$uid')
    ORDER BY id ASC
");

while ($row = mysqli_fetch_assoc($q)) {
    $time = date("h:i A", strtotime($row['created_at']));
    
    if ($row['sender_id'] == $uid) {
        // My message
        echo '<div class="message my-message">';
        echo '<div class="message-content">' . htmlspecialchars($row['message']) . '</div>';
        echo '<div class="message-time">' . $time . '</div>';
        echo '</div>';
    } else {
        // Their message
        echo '<div class="message their-message">';
        echo '<div class="message-content">' . htmlspecialchars($row['message']) . '</div>';
        echo '<div class="message-time">' . $time . '</div>';
        echo '</div>';
    }
}

// Mark as read
mysqli_query($conn, "
    UPDATE chat_messages 
    SET is_read=1 
    WHERE sender_id='$rid' 
    AND receiver_id='$uid'
");
?>