<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get owner's hotel
$hotel_result = mysqli_query($conn, "SELECT id, hotel_name FROM hotels WHERE owner_id='$owner_id' LIMIT 1");
if (mysqli_num_rows($hotel_result) == 0) {
    echo "<script>alert('Please create a hotel first!'); window.location='add_hotel.php';</script>";
    exit();
}

$hotel = mysqli_fetch_assoc($hotel_result);
$hotel_id = $hotel['id'];

// Get month filter
$selected_month = $_GET['month'] ?? date('Y-m');

// Get booking stats
$report_sql = "SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status='confirmed' THEN price ELSE 0 END) as revenue
    FROM bookings 
    WHERE hotel_id='$hotel_id' AND DATE_FORMAT(check_in_date, '%Y-%m')='$selected_month'";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $report_sql));

// Get payment stats
$payment_sql = "SELECT 
    SUM(owner_amount) as earned,
    SUM(CASE WHEN owner_paid_status='paid' THEN owner_amount ELSE 0 END) as received
    FROM user_payments 
    WHERE owner_id='$owner_id' AND DATE_FORMAT(booking_date, '%Y-%m')='$selected_month'";
$payment_stats = mysqli_fetch_assoc(mysqli_query($conn, $payment_sql));

// Get recent bookings
$recent_sql = "SELECT b.*, u.name as user_name 
               FROM bookings b
               JOIN users u ON b.user_id=u.id
               WHERE b.hotel_id='$hotel_id' 
               AND DATE_FORMAT(b.check_in_date, '%Y-%m')='$selected_month'
               ORDER BY b.id DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_sql);

// Get available months
$months_result = mysqli_query($conn, 
    "SELECT DISTINCT DATE_FORMAT(check_in_date, '%Y-%m') as month 
     FROM bookings WHERE hotel_id='$hotel_id' ORDER BY month DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - <?php echo $hotel['hotel_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .card { border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-value { font-size: 24px; font-weight: bold; }
        .stat-label { color: #6c757d; font-size: 14px; }
        .badge-confirmed { background: #28a745; }
        .badge-pending { background: #ffc107; }
        .badge-cancelled { background: #dc3545; }
        @media print {
            .no-print { display: none; }
            .card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4><i class="bi bi-graph-up"></i> Hotel Report</h4>
            <p class="text-muted mb-0"><?php echo $hotel['hotel_name']; ?></p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-success"><i class="bi bi-printer"></i> Print</button>
            <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card no-print">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Select Month:</label>
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        <?php while($row = mysqli_fetch_assoc($months_result)): ?>
                            <option value="<?php echo $row['month']; ?>" <?php echo $row['month']==$selected_month?'selected':''; ?>>
                                <?php echo date('F Y', strtotime($row['month'].'-01')); ?>
                            </option>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($months_result)==0): ?>
                            <option value="<?php echo date('Y-m'); ?>" selected><?php echo date('F Y'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="stat-value text-primary"><?php echo $stats['total_bookings']??0; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="stat-value text-success"><?php echo $stats['confirmed']??0; ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="stat-value text-info">৳ <?php echo number_format($stats['revenue']??0,2); ?></div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="stat-value" style="color:#27ae60;">৳ <?php echo number_format($payment_stats['earned']??0,2); ?></div>
                    <div class="stat-label">Your Earnings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Financial Summary - <?php echo date('F Y', strtotime($selected_month.'-01')); ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount (৳)</th>
                    </tr>
                    <tr>
                        <td>Total Booking Revenue</td>
                        <td class="text-end"><?php echo number_format($stats['revenue']??0,2); ?></td>
                    </tr>
                    <tr>
                        <td>Your Share (90%)</td>
                        <td class="text-end" style="color:#27ae60;"><?php echo number_format($payment_stats['earned']??0,2); ?></td>
                    </tr>
                    <tr>
                        <td>Received from Admin</td>
                        <td class="text-end text-success"><?php echo number_format($payment_stats['received']??0,2); ?></td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Pending Payment</strong></td>
                        <td class="text-end"><strong><?php echo number_format(($payment_stats['earned']??0)-($payment_stats['received']??0),2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Bookings</h5>
        </div>
        <div class="card-body">
            <?php if(mysqli_num_rows($recent_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Check-in</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = mysqli_fetch_assoc($recent_result)): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td><?php echo $booking['user_name']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($booking['check_in_date'])); ?></td>
                                <td>৳ <?php echo number_format($booking['price'],2); ?></td>
                                <td>
                                    <?php if($booking['status']=='confirmed'): ?>
                                        <span class="badge badge-confirmed">Confirmed</span>
                                    <?php elseif($booking['status']=='pending'): ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No bookings found</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-muted mt-4 no-print">
        <p>Report generated on <?php echo date('d F Y h:i A'); ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-refresh on month change
    document.querySelector('[name="month"]').addEventListener('change', function(){
        this.form.submit();
    });
</script>
</body>
</html>