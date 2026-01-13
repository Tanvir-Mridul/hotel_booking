<?php
session_start();
include "../db_connect.php";
include "sidebar.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Pay to owner
if (isset($_GET['pay'])) {
    $payment_id = intval($_GET['pay']);
    
    // Update payment status
    mysqli_query($conn, "UPDATE user_payments SET owner_paid_status='paid', owner_paid_date=NOW() WHERE id='$payment_id'");
    
    header("Location: manage_payments.php?msg=paid");
    exit();
}

// Fetch user payments with commission
$sql = "SELECT up.*, u.name as user_name, o.name as owner_name
        FROM user_payments up
        JOIN users u ON up.user_id = u.id
        JOIN users o ON up.owner_id = o.id
        ORDER BY up.id DESC";
$result = mysqli_query($conn, $sql);

// Calculate totals
$total_sql = "SELECT 
    SUM(amount) as total_amount,
    SUM(commission) as total_commission,
    SUM(owner_amount) as total_owner_amount
    FROM user_payments";
$total_result = mysqli_query($conn, $total_sql);
$totals = mysqli_fetch_assoc($total_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; }
        .card { box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .badge-pending { background: #ffc107; }
        .badge-paid { background: #28a745; color: white; }
        .summary-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-label { color: #666; font-size: 14px; }
        .summary-value { font-size: 18px; font-weight: bold; }
        .commission-badge { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 10px; font-size: 12px; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <!-- Summary -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Total Received</div>
            <div class="summary-value text-success">৳ <?= number_format($totals['total_amount'] ?? 0, 2) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Commission</div>
            <div class="summary-value text-info">৳ <?= number_format($totals['total_commission'] ?? 0, 2) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">To Pay Owners</div>
            <div class="summary-value text-warning">৳ <?= number_format($totals['total_owner_amount'] ?? 0, 2) ?></div>
        </div>
    </div>
    
    <!-- Payments Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">💰 User Payments</h4>
        </div>
        
        <div class="card-body">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'paid'): ?>
                <div class="alert alert-success">Payment marked as paid!</div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User → Owner</th>
                            <th>Hotel</th>
                            <th>Amount Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <strong><?= $row['user_name'] ?></strong><br>
                                    <small>→ <?= $row['owner_name'] ?></small>
                                </td>
                                <td><?= $row['hotel_name'] ?></td>
                                <td>
                                    <strong class="text-success">৳ <?= number_format($row['amount'], 2) ?></strong><br>
                                    <small>Commission: <span class="text-danger">৳<?= number_format($row['commission'], 2) ?></span></small><br>
                                    <small>Owner gets: <span class="text-info">৳<?= number_format($row['owner_amount'], 2) ?></span></small>
                                </td>
                                <td>
                                    <?php if($row['owner_paid_status'] == 'paid'): ?>
                                        <span class="badge badge-paid">Paid to Owner</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if($row['owner_paid_status'] == 'pending'): ?>
                                        <a href="?pay=<?= $row['id'] ?>" 
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('Pay ৳<?= number_format($row['owner_amount'], 2) ?> to <?= $row['owner_name'] ?>?')">
                                           Pay Owner
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Paid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No payments found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>