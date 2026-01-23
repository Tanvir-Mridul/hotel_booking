<?php


// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}



$current_page = basename($_SERVER['PHP_SELF']);



?>

<div class="sidebar">
    <div class="sidebar-header">
        <h4>Admin Panel</h4>
        <small>Welcome Admin</small>
    </div>

    <div class="sidebar-links">
     
        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">📊 Dashboard</a>
        <a href="hotels.php" class="<?= $current_page == 'hotels.php' ? 'active' : '' ?>">🏨 Manage Hotels</a>

        <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">👥 Manage Users</a>
         <!-- NEW MENU ITEM -->
    <a href="manage_payments.php" class="<?= $current_page == 'manage_payments.php' ? 'active' : '' ?>">💰 User Payments</a>
        <a href="manage_subscriptions.php" class="<?= $current_page == 'manage_subscriptions.php' ? 'active' : '' ?>">📅
            Manage Subscription</a>
        <a href="manage_packages.php" class="<?= $current_page == 'manage_packages.php' ? 'active' : '' ?>">📦 Subscription
            Packages</a>
            <a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">📦 Reports
            Packages</a>
            



        <a href="../index.php" class="home">🏠 Home</a>
        <a href="../logout.php" class="logout">🚪 Logout</a>

    </div>
</div>

<style>
    .sidebar {
        width: 220px;
        height: 100vh;
        background: #2c3e50;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
    }


    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h4 {
        margin: 0;
        font-weight: 700;
        color: #fff;
    }

    .sidebar-header small {
        color: #aaa;
    }

    .sidebar-links {
        padding: 20px 0;
        display: flex;
        flex-direction: column;

    }

    .sidebar-links a {
        display: block;
        color: #ecf0f1;
        padding: 12px 20px;
        text-decoration: none;
        margin: 5px 10px;
        border-radius: 6px;
        transition: all 0.3s;
    }

    .sidebar-links a:hover {
        background: #3498db;
        color: #fff;
        transform: translateX(3px);
        text-decoration: none;
    }

    .sidebar-links a.active {
        background: #2980b9;
        color: #fff;
        font-weight: 600;
        text-decoration: none;
    }

    .sidebar-links a.logout {
        background: #e74c3c;
        margin-top: auto;
        margin-bottom: 20px;
    }

    .sidebar-links a.logout:hover {
        background: #c0392b;
    }

    .sidebar-links a.home {
        background: #e6079bff;
        margin-top: 60px;
        margin-bottom: 5px;
    }

    .sidebar-links a.home:hover {
        background: #f0084dff;
    }



</style>