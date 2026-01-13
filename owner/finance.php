<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Calculate earnings
$earned_sql = "SELECT 
    SUM(amount) as total_earned,
    SUM(commission) as total_commission,
    SUM(owner_amount) as net_earned,
    SUM(CASE WHEN owner_paid_status='paid' THEN owner_amount ELSE 0 END) as total_paid
    FROM user_payments 
    WHERE owner_id='$owner_id'";
$earned_result = mysqli_query($conn, $earned_sql);
$earned = mysqli_fetch_assoc($earned_result);

if (!$earned) {
    $earned = ['total_earned' => 0, 'total_commission' => 0, 'net_earned' => 0, 'total_paid' => 0];
}

// Get payments
$payments_sql = "SELECT * FROM user_payments 
                 WHERE owner_id='$owner_id' 
                 ORDER BY created_at DESC LIMIT 10";
$payments_result = mysqli_query($conn, $payments_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Finance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .container { max-width: 800px; }
        .balance-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .balance-box { text-align: center; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        .balance-amount { font-size: 28px; font-weight: bold; margin-bottom: 5px; }
        .balance-label { color: #666; font-size: 14px; }
        .table th { border-top: none; }
        .badge-paid { background: #28a745; }
        .badge-pending { background: #ffc107; }
    </style>
    <?php include "../header.php"; ?>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">
    <h3 class="mb-4">💰 Owner Finance</h3>
    
    <!-- Balance Summary -->
    <div class="balance-card">
        <h5 class="mb-3">Financial Summary</h5>
        
        <div class="row">
            <div class="col-md-3">
                <div class="balance-box" style="background:#f0f8ff;">
                    <div class="balance-amount text-primary">৳ <?= number_format($earned['total_earned'], 2) ?></div>
                    <div class="balance-label">Total Bookings</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="balance-box" style="background:#fff0f0;">
                    <div class="balance-amount text-danger">৳ <?= number_format($earned['total_commission'], 2) ?></div>
                    <div class="balance-label">Commission (10%)</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="balance-box" style="background:#f0fff0;">
                    <div class="balance-amount text-success">৳ <?= number_format($earned['net_earned'], 2) ?></div>
                    <div class="balance-label">Net Earned</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="balance-box" style="background:#fff8f0;">
                    <div class="balance-amount text-warning">৳ <?= number_format($earned['total_paid'], 2) ?></div>
                    <div class="balance-label">Total Paid</div>
                </div>
            </div>
        </div>
        
        <!-- Available Balance -->
        <div class="mt-3 p-3 text-center" style="background:#e8f5e9; border-radius:8px;">
            <h4 class="mb-0">
                Available Balance: 
                <span class="text-success">৳ <?= number_format($earned['net_earned'] - $earned['total_paid'], 2) ?></span>
            </h4>
            <small>Net Earned - Total Paid</small>
        </div>
    </div>
    
    <!-- Payment History -->
    <div class="balance-card">
        <h5 class="mb-3">Recent Payments</h5>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Hotel</th>
                    <th>Amount</th>
                    <th>Commission</th>
                    <th>You Get</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($payments_result) > 0): ?>
                    <?php while($payment = mysqli_fetch_assoc($payments_result)): ?>
                    <tr>
                        <td><?= date('d M', strtotime($payment['created_at'])) ?></td>
                        <td><?= $payment['hotel_name'] ?></td>
                        <td class="text-primary">৳ <?= number_format($payment['amount'], 2) ?></td>
                        <td class="text-danger">-৳ <?= number_format($payment['commission'], 2) ?></td>
                        <td class="text-success">৳ <?= number_format($payment['owner_amount'], 2) ?></td>
                        <td>
                            <?php if($payment['owner_paid_status'] == 'paid'): ?>
                                <span class="badge badge-paid">Paid</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No payments yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>