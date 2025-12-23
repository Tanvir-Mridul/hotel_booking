<?php
// Include database for stats
include "../db_connect.php";

$owner_id = $_SESSION['user_id'];
$owner_name = $_SESSION['name'];

// Count stats
$total_flats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM hotels WHERE owner_id='$owner_id'"))['total'];

$approved_flats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as approved FROM hotels WHERE owner_id='$owner_id' AND status='approved'"))['approved'];

$pending_flats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as pending FROM hotels WHERE owner_id='$owner_id' AND status='pending'"))['pending'];
?>

<div class="sidebar">
    <!-- Owner Info -->
    <div class="owner-info">
        <div class="owner-icon">👤</div>
        <div class="owner-name"><?php echo $owner_name; ?></div>
        <div class="owner-id">ID: <?php echo $owner_id; ?></div>
    </div>
    
    <!-- Menu -->
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item">
            <span class="menu-icon">🏠</span>
            <span class="menu-text">Your Flats</span>
        </a>
        
        <a href="upload_flat.php" class="menu-item">
            <span class="menu-icon">➕</span>
            <span class="menu-text">Upload Flat</span>
        </a>
        
        <a href="../logout.php" class="menu-item logout-btn">
            <span class="menu-icon">🚪</span>
            <span class="menu-text">Logout</span>
        </a>
    </div>
</div>

<style>
/* Sidebar Styles */
.sidebar {
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #2c3e50, #1a252f);
    color: white;
    padding: 20px;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
}

/* Owner Info */
.owner-info {
    text-align: center;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    margin-bottom: 20px;
}

.owner-icon {
    font-size: 40px;
    color: #3498db;
    margin-bottom: 10px;
}

.owner-name {
    font-size: 18px;
    font-weight: bold;
    margin: 10px 0 5px;
}

.owner-id {
    font-size: 12px;
    color: #aaa;
}

/* Menu */
.sidebar-menu {
    margin-top: 30px;
}

.menu-item {
    display: flex;
    align-items: center;
    color: white;
    padding: 12px 15px;
    text-decoration: none;
    margin: 5px 0;
    border-radius: 5px;
    transition: all 0.3s;
}

.menu-item:hover {
    background: #3498db;
}

.menu-item.active {
    background: #3498db;
}

.menu-icon {
    font-size: 18px;
    margin-right: 10px;
    width: 24px;
    text-align: center;
}

.menu-text {
    font-size: 14px;
}

.logout-btn {
    background: #e74c3c;
    color: white;
    margin-top: 20px;
}

.logout-btn:hover {
    background: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        padding: 15px 10px;
    }
    
    .owner-info, .menu-text {
        display: none;
    }
    
    .menu-item {
        justify-content: center;
        padding: 15px 5px;
    }
    
    .menu-icon {
        margin-right: 0;
        font-size: 20px;
    }
}
</style>