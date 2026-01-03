<?php
session_start();

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include "../db_connect.php";

include "../cron/expire_subscriptions.php";

// Count stats
$total_hotels = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hotels"))['total'];
$pending_hotels = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as pending FROM hotels WHERE status='pending'"))['pending'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_subscriptions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM owner_subscriptions"))['total']; // <-- এইটা নতুন

// Total subscription revenue
$total_subscription_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(s.price) as total 
    FROM owner_subscriptions os
    JOIN subscriptions s ON os.package_id = s.id
    WHERE os.status='approved'
"))['total'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            margin: 0;
            background: #f5f5f5;
        }

        .main {
            margin-left: 50px;
            margin-top: 60px;
            padding: 20px;
            width: 100%;
        }

        /* Stats Cards */
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        .total-hotels .stat-number {
            color: #3498db;
        }

        .pending .stat-number {
            color: #f39c12;
        }

        .users .stat-number {
            color: #9b59b6;
        }

        .bookings .stat-number {
            color: #27ae60;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Quick Actions */
        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .action-btn {
            padding: 15px;
            background: white;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover {
            background: #f8f9fa;
        }

        /* Table */
        .table-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-approved {
            background: #d4edda;
            color: #155724;
        }

        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="main">
        <h3>Admin Dashboard</h3>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-card total-hotels">
                <div class="stat-number"><?php echo $total_hotels; ?></div>
                <div class="stat-label">Total Hotels</div>
            </div>

            <div class="stat-card pending">
                <div class="stat-number"><?php echo $pending_hotels; ?></div>
                <div class="stat-label">Pending Approval</div>
            </div>

            <div class="stat-card users">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card bookings">
                <div class="stat-number"><?php echo $total_subscriptions; ?></div>
                <div class="stat-label">Total Subscriptions</div>
            </div>
            <div class="stat-card bookings" style="background:#ffe0b2;">
                <div class="stat-number">৳ <?php echo number_format($total_subscription_revenue, 2); ?></div>
                <div class="stat-label">Total Subscription Revenue</div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="actions">
            <a href="hotels.php" class="action-btn">
                <div style="font-size: 30px;">🏨</div>
                <div>Manage Hotels</div>
            </a>

            <a href="users.php" class="action-btn">
                <div style="font-size: 30px;">👥</div>
                <div>Manage Users</div>
            </a>

            <a href="manage_subscriptions.php" class="action-btn">
                <div style="font-size: 30px;">📅</div>
                <div>Manage Subscription</div>
            </a>
        </div>

        <!-- Recent Pending Hotels -->
        <div class="table-box">
            <h5>Recent Pending Hotels</h5>
            <?php
            $pending_sql = "SELECT hotels.*, users.name as owner_name 
                       FROM hotels 
                       JOIN users ON hotels.owner_id = users.id 
                       WHERE hotels.status='pending' 
                       ORDER BY hotels.id DESC LIMIT 5";
            $pending_result = mysqli_query($conn, $pending_sql);

            if (mysqli_num_rows($pending_result) > 0):
                ?>
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Hotel</th>
                        <th>Owner</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($pending_result)): ?>
                        <tr>
                            <td><?php echo $row['hotel_name']; ?></td>
                            <td><?php echo $row['owner_name']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td>৳ <?php echo $row['price']; ?></td>
                            <td>
                                <a href="approve_hotel.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-success btn-sm">Approve</a>
                                <a href="reject_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <a href="hotels.php" class="btn btn-primary btn-sm">View All</a>
            <?php else: ?>
                <p class="text-muted mt-3">No pending hotels</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>