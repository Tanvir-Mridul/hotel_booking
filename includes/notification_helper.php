<?php
// includes/notification_helper.php
function sendNotification($receiver_id, $receiver_role, $message, $link = '#') {
    global $conn;
    
    // Debug log
    error_log("Sending Notification: Receiver=$receiver_id, Role=$receiver_role, Message=$message");
    
    $sql = "INSERT INTO notifications (receiver_id, receiver_role, message, link, status, created_at) 
            VALUES (?, ?, ?, ?, 'unread', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $receiver_id, $receiver_role, $message, $link);
    
    if ($stmt->execute()) {
        error_log("Notification sent successfully - ID: " . $stmt->insert_id);
        return true;
    } else {
        error_log("Notification failed: " . $stmt->error);
        return false;
    }
}
?>