<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Calculate earnings from user_payments table
$earned_sql = "SELECT 
    COALESCE(SUM(amount), 0) as total_received,
    COALESCE(SUM(commission), 0) as total_commission,
    COALESCE(SUM(owner_amount), 0) as net_earned,
    COALESCE(SUM(CASE WHEN owner_paid_status='paid' THEN owner_amount ELSE 0 END), 0) as total_paid,
    COALESCE(SUM(CASE WHEN owner_paid_status='pending' THEN owner_amount ELSE 0 END), 0) as pending_payment,
    COUNT(*) as total_bookings
    FROM user_payments 
    WHERE owner_id='$owner_id' 
    AND payment_status='success'";
    
$earned_result = mysqli_query($conn, $earned_sql);
$earned = mysqli_fetch_assoc($earned_result);

// Default values if no data
if (!$earned) {
    $earned = [
        'total_received' => 0,
        'total_commission' => 0,
        'net_earned' => 0,
        'total_paid' => 0,
        'pending_payment' => 0,
        'total_bookings' => 0
    ];
}

// Get recent payments
$payments_sql = "SELECT * FROM user_payments 
                 WHERE owner_id='$owner_id' 
                 AND payment_status='success'
                 ORDER BY created_at DESC LIMIT 10";
$payments_result = mysqli_query($conn, $payments_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Finance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .container { max-width: 900px; }
        .balance-card { 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
        }
        .balance-box { 
            text-align: center; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .balance-box:hover {
            transform: translateY(-5px);
        }
        .balance-amount { 
            font-size: 28px; 
            font-weight: bold; 
            margin-bottom: 5px; 
        }
        .balance-label { 
            color: #666; 
            font-size: 14px; 
            margin-bottom: 5px;
        }
        .table th { border-top: none; background: #f8f9fa; }
        .badge-paid { background: #28a745; color: white; }
        .badge-pending { background: #ffc107; color: #212529; }
        .amount-cell { text-align: right; font-family: 'Courier New', monospace; }
        .summary-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-4">💰 Owner Finance Dashboard</h3>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    <!-- Summary Cards -->
    <div class="summary-row">
        <div class="summary-card" style="border-top: 4px solid #3498db;">
            <div class="summary-number text-primary">৳ <?= number_format($earned['total_received'], 2) ?></div>
            <div class="balance-label">Total Bookings Amount</div>
            <small class="text-muted">What users paid</small>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #e74c3c;">
            <div class="summary-number text-danger">৳ <?= number_format($earned['total_commission'], 2) ?></div>
            <div class="balance-label">Platform Commission</div>
            <small class="text-muted">10% deducted by admin</small>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #2ecc71;">
            <div class="summary-number text-success">৳ <?= number_format($earned['net_earned'], 2) ?></div>
            <div class="balance-label">Your Total Earnings</div>
            <small class="text-muted">After commission</small>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #f39c12;">
            <div class="summary-number"><?= $earned['total_bookings'] ?></div>
            <div class="balance-label">Total Bookings</div>
            <small class="text-muted">Successful payments</small>
        </div>
    </div>
    
    <!-- Payment Status -->
    <div class="balance-card">
        <h5 class="mb-3"><i class="fas fa-money-check-alt"></i> Payment Status</h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="balance-box" style="background:#e8f5e9;">
                    <div class="balance-amount text-success">৳ <?= number_format($earned['total_paid'], 2) ?></div>
                    <div class="balance-label">✅ Received Payments</div>
                    <small>Already paid to you by admin</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="balance-box" style="background:#fff3cd;">
                    <div class="balance-amount text-warning">৳ <?= number_format($earned['pending_payment'], 2) ?></div>
                    <div class="balance-label">⏳ Pending Payments</div>
                    <small>Waiting for admin to pay you</small>
                </div>
            </div>
        </div>
        
        <!-- Available Balance -->
        <div class="mt-3 p-3 text-center" style="background:#e3f2fd; border-radius:8px;">
            <h4 class="mb-0">
                <i class="fas fa-wallet"></i> 
                Available Balance: 
                <span class="text-primary">৳ <?= number_format($earned['pending_payment'], 2) ?></span>
            </h4>
            <small>Amount that admin will pay you soon</small>
        </div>
        
        <!-- Commission Breakdown -->
        <div class="mt-4 p-3" style="background:#f8f9fa; border-radius:8px;">
            <h6><i class="fas fa-percentage"></i> Commission Breakdown (Per Booking):</h6>
            <div class="row text-center">
                <div class="col-md-4">
                    <div style="font-size: 14px; color: #666;">User Pays</div>
                    <div style="font-size: 18px; font-weight: bold; color: #3498db;">100%</div>
                    <small>Full amount</small>
                </div>
                <div class="col-md-4">
                    <div style="font-size: 14px; color: #666;">Admin Commission</div>
                    <div style="font-size: 18px; font-weight: bold; color: #e74c3c;">-10%</div>
                    <small>Platform fee</small>
                </div>
                <div class="col-md-4">
                    <div style="font-size: 14px; color: #666;">You Receive</div>
                    <div style="font-size: 18px; font-weight: bold; color: #2ecc71;">90%</div>
                    <small>Your earnings</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Payments -->
    <div class="balance-card">
        <h5 class="mb-3"><i class="fas fa-history"></i> Recent Payments</h5>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Hotel/Room</th>
                    <th class="amount-cell">Amount Details</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($payments_result) > 0): ?>
                    <?php while($payment = mysqli_fetch_assoc($payments_result)): ?>
                    <tr>
                        <td>
                            <?= date('d M', strtotime($payment['created_at'])) ?><br>
                            <small class="text-muted"><?= date('h:i A', strtotime($payment['created_at'])) ?></small>
                        </td>
                        <td>
                            <strong><?= $payment['hotel_name'] ?></strong>
                            <?php if(!empty($payment['room_title'])): ?>
                                <br><small class="text-muted"><?= $payment['room_title'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="amount-cell">
                            <div class="text-primary">
                                <strong>৳ <?= number_format($payment['amount'], 2) ?></strong>
                            </div>
                            <div class="text-danger" style="font-size: 12px;">
                                - Commission: ৳<?= number_format($payment['commission'], 2) ?>
                            </div>
                            <div class="text-success" style="font-size: 12px;">
                                → You get: ৳<?= number_format($payment['owner_amount'], 2) ?>
                            </div>
                        </td>
                        <td>
                            <?php if($payment['owner_paid_status'] == 'paid'): ?>
                                <span class="badge badge-paid">
                                    <i class="fas fa-check"></i> Paid
                                </span><br>
                                <small class="text-muted">
                                    <?= date('d M', strtotime($payment['owner_paid_date'])) ?>
                                </small>
                            <?php else: ?>
                                <span class="badge badge-pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= $payment['receipt_id'] ?></small>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-money-bill-wave fa-2x mb-3"></i><br>
                            No payments yet
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Info Box -->
    <div class="alert alert-info mt-3">
        <h6><i class="fas fa-info-circle"></i> How it works:</h6>
        <ol class="mb-0">
            <li><strong>User books & pays 100%</strong> - Customer pays full amount for booking</li>
            <li><strong>Admin keeps 10% commission</strong> - Platform fee deducted automatically</li>
            <li><strong>You earn 90%</strong> - Your share of the payment</li>
            <li><strong>Admin pays you</strong> - Admin will send your 90% when ready</li>
        </ol>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-refresh page every 30 seconds to check for new payments
setTimeout(function() {
    location.reload();
}, 30000);
</script>
</body>
</html>