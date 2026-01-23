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

// Subscription stats 
$subscription_stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_subscriptions,
        SUM(s.price) as total_subscription_revenue,
        COUNT(CASE WHEN os.status='approved' THEN 1 END) as active_subscriptions
    FROM owner_subscriptions os
    JOIN subscriptions s ON os.package_id = s.id
    WHERE os.status != 'rejected'
"));

// Booking payment stats 
$booking_payment_stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status='confirmed' THEN price ELSE 0 END) as total_booking_revenue,
        COUNT(CASE WHEN status='confirmed' THEN 1 END) as confirmed_bookings
    FROM bookings
    WHERE status IN ('confirmed', 'pending')
"));

// Commission stats - 
$commission_stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        SUM(commission) as total_commission,
        SUM(owner_amount) as total_paid_to_owners,
        SUM(amount) as total_received_amount,
        COUNT(*) as total_payments
    FROM user_payments
    WHERE payment_status = 'success'
"));

// Total revenue calculation
$total_subscription_revenue = $subscription_stats['total_subscription_revenue'] ?? 0;
$total_booking_revenue = $booking_payment_stats['total_booking_revenue'] ?? 0;
$total_commission = $commission_stats['total_commission'] ?? 0;
$total_paid_to_owners = $commission_stats['total_paid_to_owners'] ?? 0;
$total_received = $commission_stats['total_received_amount'] ?? 0;

// Net profit calculation
$net_profit = $total_commission + $total_subscription_revenue;

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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
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

        .subscription .stat-number {
            color: #e74c3c;
        }

        .booking-revenue .stat-number {
            color: #27ae60;
        }

        .commission .stat-number {
            color: #9b59b6;
        }

        .net-profit .stat-number {
            color: #2ecc71;
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
            margin-bottom: 20px;
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

        /* Summary Box */
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .summary-title {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-detail {
            font-size: 12px;
            opacity: 0.8;
        }

        .money-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="main">
        <h3>Admin Dashboard</h3>

        <!-- Financial Summary -->
        <div class="summary-box">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div class="money-icon">💰</div>
                    <div class="summary-title">Total Net Profit</div>
                    <div class="summary-value">৳ <?php echo number_format($net_profit, 2); ?></div>
                    <div class="summary-detail">Commission + Subscription Revenue</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="money-icon">💳</div>
                    <div class="summary-title">Total Received</div>
                    <div class="summary-value">৳ <?php echo number_format($total_received, 2); ?></div>
                    <div class="summary-detail">From users</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="money-icon">🏦</div>
                    <div class="summary-title">Paid to Owners</div>
                    <div class="summary-value">৳ <?php echo number_format($total_paid_to_owners, 2); ?></div>
                    <div class="summary-detail">90% of total payments</div>
                </div>
            </div>
        </div>

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

            <!-- Subscription Stats -->
            <div class="stat-card subscription">
                <div class="stat-number">৳ <?php echo number_format($total_subscription_revenue, 2); ?></div>
                <div class="stat-label">Subscription Revenue</div>
                <small><?php echo $subscription_stats['active_subscriptions'] ?? 0; ?> active</small>
            </div>

            <!-- Booking Payment Stats -->
            <div class="stat-card booking-revenue">
                <div class="stat-number">৳ <?php echo number_format($total_booking_revenue, 2); ?></div>
                <div class="stat-label">Booking Revenue</div>
                <small><?php echo $booking_payment_stats['confirmed_bookings'] ?? 0; ?> confirmed</small>
            </div>

            <!-- Commission Stats -->
            <div class="stat-card commission">
                <div class="stat-number">৳ <?php echo number_format($total_commission, 2); ?></div>
                <div class="stat-label">Your Commission</div>
                <small>10% of payments</small>
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

            <a href="reports.php" class="action-btn">
                <div style="font-size: 30px;">📊</div>
                <div>View Reports</div>
            </a>

            <a href="manage_payments.php" class="action-btn">
                <div style="font-size: 30px;">💰</div>
                <div>Manage Payments</div>
            </a>
        </div>

        <!-- Financial Breakdown -->
        <div class="table-box">
            <h5>Financial Breakdown</h5>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Subscription Revenue</td>
                        <td class="text-success">৳ <?php echo number_format($total_subscription_revenue, 2); ?></td>
                        <td>From <?php echo $subscription_stats['total_subscriptions'] ?? 0; ?> subscriptions</td>
                    </tr>
                    <tr>
                        <td>Total Booking Revenue</td>
                        <td class="text-success">৳ <?php echo number_format($total_booking_revenue, 2); ?></td>
                        <td>From <?php echo $booking_payment_stats['confirmed_bookings'] ?? 0; ?> confirmed bookings
                        </td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Total Gross Revenue</strong></td>
                        <td class="text-primary"><strong>৳ <?php echo number_format($total_received, 2); ?></strong>
                        </td>
                        <td><strong>Total received from users</strong></td>
                    </tr>
                    <tr>
                        <td>Your Commission (10%)</td>
                        <td class="text-danger">৳ <?php echo number_format($total_commission, 2); ?></td>
                        <td>10% of total payments</td>
                    </tr>
                    <tr>
                        <td>Paid to Owners (90%)</td>
                        <td class="text-info">৳ <?php echo number_format($total_paid_to_owners, 2); ?></td>
                        <td>90% of total payments</td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Your Net Profit</strong></td>
                        <td class="text-success"><strong>৳ <?php echo number_format($net_profit, 2); ?></strong></td>
                        <td><strong>Commission + Subscription Revenue</strong></td>
                    </tr>
                </tbody>
            </table>
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