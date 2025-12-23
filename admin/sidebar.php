<?php
// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<div class="sidebar">
    <div style="padding: 20px; text-align: center; border-bottom: 1px solid #444; background: #2c3e50;">
        <h4 style="color: white; margin: 0;">Admin Panel</h4>
        <small style="color: #aaa;">Welcome Admin</small>
    </div>
    
    <div style="padding: 20px;">
        <a href="dashboard.php" 
           style="display: block; color: white; padding: 12px; text-decoration: none; margin: 5px 0; border-radius: 5px; background: #3498db;">
            📊 Dashboard
        </a>
        
        <a href="hotels.php" 
           style="display: block; color: white; padding: 12px; text-decoration: none; margin: 5px 0; border-radius: 5px;">
            🏨 Manage Hotels
        </a>
        
        <a href="users.php" 
           style="display: block; color: white; padding: 12px; text-decoration: none; margin: 5px 0; border-radius: 5px;">
            👥 Manage Users
        </a>
        
        <a href="bookings.php" 
           style="display: block; color: white; padding: 12px; text-decoration: none; margin: 5px 0; border-radius: 5px;">
            📅 Manage Bookings
        </a>
        
        <a href="../logout.php" 
           style="display: block; color: white; padding: 12px; text-decoration: none; margin: 5px 0; border-radius: 5px; background: #e74c3c; margin-top: 20px;">
            🚪 Logout
        </a>
    </div>
</div>

<style>
.sidebar {
    width: 220px;
    height: 100vh;
    background: #34495e;
    position: fixed;
    left: 0;
    top: 0;
}
</style>