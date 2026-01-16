<?php
// admin/reports.php - SIMPLE VERSION WITH PRINT
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Default filter: current month
$current_month = date('Y-m');
$selected_month = isset($_GET['month']) ? $_GET['month'] : $current_month;
$month_name = date('F Y', strtotime($selected_month . '-01'));

// SIMPLE: Get booking statistics (using check_in_date)
$report_sql = "SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN price ELSE 0 END) as total_revenue
    FROM bookings 
    WHERE DATE_FORMAT(check_in_date, '%Y-%m') = '$selected_month'";

$report_result = mysqli_query($conn, $report_sql);
$stats = mysqli_fetch_assoc($report_result);

// SIMPLE: Get commission data
$commission_sql = "SELECT 
    SUM(commission) as total_commission,
    SUM(owner_amount) as total_owner_amount,
    COUNT(*) as total_payments
    FROM user_payments 
    WHERE payment_status = 'success' 
    AND DATE_FORMAT(booking_date, '%Y-%m') = '$selected_month'";

$commission_result = mysqli_query($conn, $commission_sql);
$commission = mysqli_fetch_assoc($commission_result);

// SIMPLE: Get subscription data - FIXED: removed created_at
$subscription_sql = "SELECT 
    COUNT(*) as total_subscriptions,
    SUM(s.price) as subscription_revenue
    FROM owner_subscriptions os
    JOIN subscriptions s ON os.package_id = s.id
    WHERE 1=1"; // No date filter since created_at doesn't exist

$subscription_result = mysqli_query($conn, $subscription_sql);
$subscription_stats = mysqli_fetch_assoc($subscription_result);

// SIMPLE: Get total subscription revenue (all time)
$total_subscription_sql = "SELECT 
    SUM(s.price) as total_subscription_revenue,
    COUNT(*) as all_time_subscriptions
    FROM owner_subscriptions os
    JOIN subscriptions s ON os.package_id = s.id
    WHERE os.status != 'rejected'";

$total_subscription_result = mysqli_query($conn, $total_subscription_sql);
$total_subscription = mysqli_fetch_assoc($total_subscription_result);

// SIMPLE: Get recent bookings
$recent_bookings_sql = "SELECT b.*, u.name as user_name, h.hotel_name 
                       FROM bookings b
                       JOIN users u ON b.user_id = u.id
                       JOIN hotels h ON b.hotel_id = h.id
                       WHERE DATE_FORMAT(b.check_in_date, '%Y-%m') = '$selected_month'
                       ORDER BY b.id DESC 
                       LIMIT 10";
$recent_bookings_result = mysqli_query($conn, $recent_bookings_sql);

// SIMPLE: Get all months with booking data for dropdown
$months_sql = "SELECT DISTINCT DATE_FORMAT(check_in_date, '%Y-%m') as month 
              FROM bookings 
              WHERE check_in_date IS NOT NULL
              ORDER BY month DESC";
$months_result = mysqli_query($conn, $months_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports - <?php echo $month_name; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Hide elements when printing */
        @media print {
            .no-print, .sidebar, .month-selector .btn, .back-button {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .report-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }
            .print-footer {
                display: block !important;
                text-align: center;
                margin-top: 30px;
                border-top: 1px solid #ddd;
                padding-top: 15px;
                font-size: 12px;
                color: #666;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .table {
                border: 1px solid #ddd;
            }
            .badge {
                border: 1px solid #000;
                background: white !important;
                color: black !important;
            }
        }
        
        /* Screen styles */
        body { background: #f8f9fa; }
        .main-content { padding: 20px; margin-left: 220px; }
        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .month-selector {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-pending { background: #ffc107; color: #212529; }
        .badge-confirmed { background: #28a745; color: white; }
        .badge-cancelled { background: #dc3545; color: white; }
        .print-header { display: none; }
        .print-footer { display: none; }
        .print-controls {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main-content">
    <!-- Print Header (only shows when printing) -->
    <div class="print-header">
        <h2>Hotel Booking System - Monthly Report</h2>
        <h3><?php echo $month_name; ?></h3>
        <p>Generated on: <?php echo date('d F Y h:i A'); ?></p>
    </div>
    
    <!-- Screen Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3><i class="fas fa-chart-bar"></i> Monthly Reports</h3>
        <div>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="reports.php" class="btn btn-primary">
                <i class="fas fa-sync"></i> Refresh
            </a>
        </div>
    </div>
    
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <form method="GET" class="row align-items-center">
            <div class="col-md-4">
                <label>Select Month:</label>
                <select name="month" class="form-control" required>
                    <?php while($month_row = mysqli_fetch_assoc($months_result)): 
                        $month_value = $month_row['month'];
                        $month_display_name = date('F Y', strtotime($month_value . '-01'));
                    ?>
                        <option value="<?php echo $month_value; ?>" 
                            <?php echo ($month_value == $selected_month) ? 'selected' : ''; ?>>
                            <?php echo $month_display_name; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block mt-4">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
            <div class="col-md-6 text-right">
                <h4 class="mb-0">
                    <?php echo $month_name; ?>
                </h4>
                <small class="text-muted">Monthly Report</small>
            </div>
        </form>
    </div>
    
    <!-- Report Summary -->
    <div class="report-card">
        <h4 class="mb-3">Monthly Summary - <?php echo $month_name; ?></h4>
        
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <div class="p-3 border rounded">
                    <div class="stat-number text-primary"><?php echo $stats['total_bookings'] ?? 0; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3 border rounded">
                    <div class="stat-number text-success">৳ <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
                    <div class="stat-label">Booking Revenue</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3 border rounded">
                    <div class="stat-number text-danger">৳ <?php echo number_format($commission['total_commission'] ?? 0, 2); ?></div>
                    <div class="stat-label">Your Commission</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3 border rounded">
                    <div class="stat-number" style="color: #9b59b6;">৳ <?php echo number_format($subscription_stats['subscription_revenue'] ?? 0, 2); ?></div>
                    <div class="stat-label">Subscription Revenue</div>
                </div>
            </div>
        </div>
        
        <!-- Detailed Summary -->
        <div class="row">
            <div class="col-md-6">
                <h6>Booking Details</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Confirmed Bookings:</td>
                        <td class="text-right"><?php echo $stats['confirmed_bookings'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td>Pending Bookings:</td>
                        <td class="text-right"><?php echo $stats['pending_bookings'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td>Cancelled Bookings:</td>
                        <td class="text-right"><?php echo $stats['cancelled_bookings'] ?? 0; ?></td>
                    </tr>
                    <tr class="table-secondary">
                        <td><strong>Total Bookings:</strong></td>
                        <td class="text-right"><strong><?php echo $stats['total_bookings'] ?? 0; ?></strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6>Financial Details</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Booking Revenue:</td>
                        <td class="text-right text-success">৳ <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Your Commission (10%):</td>
                        <td class="text-right text-danger">৳ <?php echo number_format($commission['total_commission'] ?? 0, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Subscription Revenue:</td>
                        <td class="text-right" style="color: #9b59b6;">৳ <?php echo number_format($subscription_stats['subscription_revenue'] ?? 0, 2); ?></td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Total Income:</strong></td>
                        <td class="text-right"><strong>
                            ৳ <?php echo number_format(($commission['total_commission'] ?? 0) + ($subscription_stats['subscription_revenue'] ?? 0), 2); ?>
                        </strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recent Bookings Table -->
    <div class="report-card">
        <h5>Recent Bookings - <?php echo $month_name; ?></h5>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Hotel</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Check-in</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($recent_bookings_result) > 0): ?>
                        <?php while($booking = mysqli_fetch_assoc($recent_bookings_result)): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo $booking['user_name']; ?></td>
                            <td><?php echo $booking['hotel_name']; ?></td>
                            <td class="text-right">৳ <?php echo number_format($booking['price'], 2); ?></td>
                            <td>
                                <?php if($booking['status'] == 'pending'): ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php elseif($booking['status'] == 'confirmed'): ?>
                                    <span class="badge badge-confirmed">Confirmed</span>
                                <?php elseif($booking['status'] == 'cancelled'): ?>
                                    <span class="badge badge-cancelled">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($booking['check_in_date'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                No bookings for <?php echo $month_name; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Subscription Information -->
    <div class="report-card">
        <h5>Subscription Information</h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <h6>Current Month</h6>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Total Subscriptions:</td>
                            <td class="text-right"><?php echo $subscription_stats['total_subscriptions'] ?? 0; ?></td>
                        </tr>
                        <tr>
                            <td>Subscription Revenue:</td>
                            <td class="text-right" style="color: #9b59b6;">
                                ৳ <?php echo number_format($subscription_stats['subscription_revenue'] ?? 0, 2); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <h6>All Time</h6>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Total Subscriptions:</td>
                            <td class="text-right"><?php echo $total_subscription['all_time_subscriptions'] ?? 0; ?></td>
                        </tr>
                        <tr>
                            <td>Total Revenue:</td>
                            <td class="text-right" style="color: #9b59b6;">
                                ৳ <?php echo number_format($total_subscription['total_subscription_revenue'] ?? 0, 2); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print Footer -->
    <div class="print-footer">
        <p>Report generated by: Hotel Booking System Admin</p>
        <p>Date: <?php echo date('d F Y h:i A'); ?> | Page 1 of 1</p>
    </div>
    
  

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Print optimization
function optimizeForPrint() {
    // Add print-specific classes
    $('body').addClass('printing');
    
    // Show print header
    $('.print-header').show();
    
    // Hide unnecessary elements
    $('.no-print').hide();
    
    // Wait a bit then print
    setTimeout(function() {
        window.print();
        
        // Restore after printing
        setTimeout(function() {
            $('body').removeClass('printing');
            $('.print-header').hide();
            $('.no-print').show();
        }, 1000);
    }, 500);
}

// Enhanced print function
window.onbeforeprint = function() {
    // Optional: Add any pre-print modifications
    console.log('Printing report for <?php echo $month_name; ?>');
};

window.onafterprint = function() {
    // Optional: Add any post-print cleanup
    console.log('Print completed');
};
</script>
</body>
</html>