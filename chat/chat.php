<?php
session_start();
require_once "../db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$my_id = $_SESSION['user_id'];
$my_role = $_SESSION['role'];

// চ্যাট পার্টনার আইডি
if ($my_role == 'user') {
    $receiver_id = $_GET['owner_id'] ?? 0;
    $receiver_role = 'owner';
} else {
    $receiver_id = $_GET['user_id'] ?? 0;
    $receiver_role = 'user';
}

if (!$receiver_id) {
    die("Invalid chat");
}

// রিসিভারের নাম
$receiver_q = mysqli_query($conn, "SELECT name FROM users WHERE id='$receiver_id'");
$receiver = mysqli_fetch_assoc($receiver_q);
$receiver_name = $receiver['name'] ?? 'Unknown';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e8eaf3ff 0%, #ffffffff 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .chat-box {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 90vh;
        }
        
        .chat-header {
            background: #4a6ee0;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .back-btn {
            color: white;
            font-size: 18px;
            text-decoration: none;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            background: white;
            color: #4a6ee0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }
        
        .chat-info h4 {
            margin: 0;
            font-size: 16px;
        }
        
        .chat-info small {
            opacity: 0.8;
            font-size: 12px;
        }
        
        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f7f7f7;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .message {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            font-size: 14px;
        }
        
        .my-message {
            background: #4a6ee0;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }
        
        .their-message {
            background: white;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .message-time {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 5px;
            text-align: right;
        }
        
        .input-area {
            padding: 15px;
            background: white;
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
        }
        
        .message-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
        }
        
        .message-input:focus {
            border-color: #4a6ee0;
        }
        
        .send-btn {
            background: #4a6ee0;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        
        .send-btn:hover {
            background: #3a5ed0;
        }
        
        /* Scrollbar */
        .messages::-webkit-scrollbar {
            width: 5px;
        }
        
        .messages::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="chat-box">
    <!-- Header -->
    <div class="chat-header">
        <a href="javascript:history.back()" class="back-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
        <div class="avatar">
            <?php echo strtoupper(substr($receiver_name, 0, 1)); ?>
        </div>
        <div class="chat-info">
            <h4><?php echo htmlspecialchars($receiver_name); ?></h4>
            <small><?php echo $receiver_role; ?></small>
        </div>
    </div>
    
    <!-- Messages -->
    <div class="messages" id="messagesArea">
        <div style="text-align: center; color: #999; padding: 20px;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    </div>
    
    <!-- Input -->
    <div class="input-area">
        <input type="text" 
               class="message-input" 
               id="messageInput" 
               placeholder="Type your message..." 
               autocomplete="off">
        <button class="send-btn" id="sendBtn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
const myId = <?php echo $my_id; ?>;
const myRole = '<?php echo $my_role; ?>';
const receiverId = <?php echo $receiver_id; ?>;
const receiverRole = '<?php echo $receiver_role; ?>';

// Load messages
function loadMessages() {
    fetch(`fetch_messages.php?rid=${receiverId}&rrole=${receiverRole}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('messagesArea').innerHTML = html;
            // Scroll to bottom
            const msgArea = document.getElementById('messagesArea');
            msgArea.scrollTop = msgArea.scrollHeight;
        });
}

// Send message
document.getElementById('sendBtn').onclick = sendMessage;
document.getElementById('messageInput').onkeypress = (e) => {
    if (e.key === 'Enter') sendMessage();
};

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message === '') return;
    
    // Show message immediately
    const msgArea = document.getElementById('messagesArea');
    const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    msgArea.innerHTML += `
        <div class="message my-message">
            ${message}
            <div class="message-time">${time}</div>
        </div>
    `;
    msgArea.scrollTop = msgArea.scrollHeight;
    
    // Send to server
    const form = new FormData();
    form.append('message', message);
    form.append('receiver_id', receiverId);
    form.append('receiver_role', receiverRole);
    
    fetch('send_message.php', {method: 'POST', body: form})
        .then(() => loadMessages());
    
    input.value = '';
    input.focus();
}

// Load messages every 2 seconds
loadMessages();
setInterval(loadMessages, 2000);
</script>

</body>
</html>