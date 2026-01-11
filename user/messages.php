<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// যেসব owner-দের সাথে চ্যাট হয়েছে
$sql = "SELECT DISTINCT u.id, u.name 
        FROM users u
        JOIN chat_messages cm ON 
            (cm.sender_id = u.id AND cm.receiver_id = '$user_id') OR 
            (cm.sender_id = '$user_id' AND cm.receiver_id = u.id)
        WHERE u.role = 'owner'
        ORDER BY u.name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include "../header.php"; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #e8eaf3ff 0%, #ffffffff 100%);
            min-height: 100vh;
        }
        
        .messages-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .messages-header {
            background: #4a6ee0;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .messages-header h3 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .messages-header p {
            opacity: 0.9;
            margin-top: 5px;
            font-size: 14px;
        }
        
        .chat-list {
            padding: 0;
        }
        
        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none;
            color: #333;
            transition: background 0.3s;
        }
        
        .chat-item:hover {
            background: #f8f9ff;
            text-decoration: none;
            color: #333;
        }
        
        .chat-item:last-child {
            border-bottom: none;
        }
        
        .chat-avatar {
            width: 50px;
            height: 50px;
            background: #4a6ee0;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .chat-details {
            flex: 1;
            overflow: hidden;
        }
        
        .chat-name {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 3px;
            color: #2c3e50;
        }
        
        .last-message {
            color: #666;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-right {
            text-align: right;
            margin-left: 10px;
            flex-shrink: 0;
        }
        
        .chat-time {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
        }
        
        .unread-badge {
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-icon {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #888;
            margin-bottom: 25px;
        }
        
        .back-btn {
            display: inline-block;
            margin: 20px 0 0 20px;
            color: #4a6ee0;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

                                                 
<div class="messages-container">
    <div class="messages-header">
        <h3><i class="fas fa-comments"></i> My Messages</h3>
        <p>Chat with hotel owners</p>
    </div>
    
    <div class="chat-list">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($owner = mysqli_fetch_assoc($result)): 
                // Unread count
                $unread_q = mysqli_query($conn, "SELECT COUNT(*) as unread FROM chat_messages 
                    WHERE sender_id = '{$owner['id']}' 
                    AND receiver_id = '$user_id' 
                    AND is_read = 0");
                $unread = mysqli_fetch_assoc($unread_q)['unread'];
                
                // Last message
                $last_msg_q = mysqli_query($conn, "SELECT message, created_at FROM chat_messages 
                    WHERE (sender_id = '{$owner['id']}' AND receiver_id = '$user_id') 
                    OR (sender_id = '$user_id' AND receiver_id = '{$owner['id']}')
                    ORDER BY id DESC LIMIT 1");
                $last_msg = mysqli_fetch_assoc($last_msg_q);
                $last_time = !empty($last_msg['created_at']) ? date("h:i A", strtotime($last_msg['created_at'])) : "";
            ?>
                <a href="../chat/chat.php?owner_id=<?= $owner['id'] ?>" class="chat-item">
                    <div class="chat-avatar">
                        <?= strtoupper(substr($owner['name'], 0, 1)) ?>
                    </div>
                    
                    <div class="chat-details">
                        <div class="chat-name"><?= htmlspecialchars($owner['name']) ?></div>
                        <div class="last-message">
                            <?= !empty($last_msg['message']) ? 
                                htmlspecialchars(substr($last_msg['message'], 0, 60)) . "..." : 
                                "No messages yet" ?>
                        </div>
                    </div>
                    
                    <div class="chat-right">
                        <div class="chat-time"><?= $last_time ?></div>
                        <?php if ($unread > 0): ?>
                            <div class="unread-badge"><?= $unread ?></div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-comments"></i>
                </div>
                <h4>No messages yet</h4>
                <p>Start a chat from your bookings</p>
                <a href="my_booking.php" style="
                    display: inline-block;
                    background: #4a6ee0;
                    color: white;
                    padding: 10px 25px;
                    border-radius: 25px;
                    text-decoration: none;
                    font-weight: 500;
                    margin-top: 10px;
                ">
                    <i class="fas fa-calendar-alt"></i> Go to Bookings
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Enter key to go back
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        history.back();
    }
});
</script>

</body>
</html>