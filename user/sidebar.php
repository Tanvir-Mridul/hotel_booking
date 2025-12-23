<?php
// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
?>

<div class="sidebar">
    <!-- User Info -->
    <div style="padding: 20px; text-align: center; background: #3498db; color: white;">
        <div style="font-size: 40px; margin-bottom: 10px;">👤</div>
        <h5 style="margin: 0;"><?php echo $user_name; ?></h5>
        <small>Regular User</small>
    </div>
    
    <!-- Menu -->
    <div style="padding: 20px;">
        <a href="dashboard.php" 
           style="display: flex; align-items: center; color: #333; padding: 12px; text-decoration: none; margin: 8px 0; border-radius: 8px; background: #e3f2fd;">
            <span style="font-size: 20px; margin-right: 10px;">🏠</span>
            <span>Dashboard</span>
        </a>
        
        <a href="../hotel/hotel_list.php" 
           style="display: flex; align-items: center; color: #333; padding: 12px; text-decoration: none; margin: 8px 0; border-radius: 8px;">
            <span style="font-size: 20px; margin-right: 10px;">🏨</span>
            <span>Browse Hotels</span>
        </a>
        
        <a href="my_booking.php" 
           style="display: flex; align-items: center; color: #333; padding: 12px; text-decoration: none; margin: 8px 0; border-radius: 8px;">
            <span style="font-size: 20px; margin-right: 10px;">📅</span>
            <span>My Bookings</span>
        </a>
        
        <a href="profile.php" 
           style="display: flex; align-items: center; color: #333; padding: 12px; text-decoration: none; margin: 8px 0; border-radius: 8px;">
            <span style="font-size: 20px; margin-right: 10px;">👤</span>
            <span>My Profile</span>
        </a>
        
        <a href="../logout.php" 
           style="display: flex; align-items: center; color: white; padding: 12px; text-decoration: none; margin: 8px 0; border-radius: 8px; background: #e74c3c; margin-top: 20px;">
            <span style="font-size: 20px; margin-right: 10px;">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
.sidebar {
    width: 220px;
    height: 100vh;
    background: white;
    position: fixed;
    left: 0;
    top: 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}
</style>