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
    
    // Get payment details
    $payment_q = mysqli_query($conn, "SELECT * FROM user_payments WHERE id='$payment_id'");
    $payment = mysqli_fetch_assoc($payment_q);
    
    // Update payment status
    mysqli_query($conn, "UPDATE user_payments SET owner_paid_status='paid', owner_paid_date=NOW() WHERE id='$payment_id'");
    
    // Insert into owner_finance
    mysqli_query($conn, "INSERT INTO owner_finance (owner_id, total_paid) 
                        VALUES ('{$payment['owner_id']}', '{$payment['owner_amount']}')
                        ON DUPLICATE KEY UPDATE 
                        total_paid = total_paid + '{$payment['owner_amount']}',
                        last_updated = NOW()");
    
    header("Location: manage_payments.php?msg=paid");
    exit();
}

// Fetch ALL user payments (booking payments only)
$sql = "SELECT up.*, 
               u.name as user_name, 
               u.email as user_email,
               o.name as owner_name,
               o.email as owner_email
        FROM user_payments up
        JOIN users u ON up.user_id = u.id
        JOIN users o ON up.owner_id = o.id
        WHERE up.payment_status = 'success'
        ORDER BY up.created_at DESC";
$result = mysqli_query($conn, $sql);

// Calculate totals
$total_sql = "SELECT 
    COUNT(*) as total_transactions,
    SUM(amount) as total_amount,
    SUM(commission) as total_commission,
    SUM(owner_amount) as total_owner_amount,
    SUM(CASE WHEN owner_paid_status = 'paid' THEN owner_amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN owner_paid_status = 'pending' THEN owner_amount ELSE 0 END) as total_pending
    FROM user_payments 
    WHERE payment_status = 'success'";
$total_result = mysqli_query($conn, $total_sql);
$totals = mysqli_fetch_assoc($total_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage User Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Segoe UI', Arial, sans-serif; }
        .main { margin-left: 220px; padding: 20px; }
        
        /* Summary Cards */
        .summary-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-label {
            color: #666;
            font-size: 14px;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        .table th {
            background: #f8f9fa;
            border-top: none;
        }
        .badge-pending { background: #ffc107; color: #212529; }
        .badge-paid { background: #28a745; color: white; }
        .amount-cell { text-align: right; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3 class="mb-4"><i class="fas fa-money-bill-wave"></i> User Payment Management</h3>
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'paid'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Payment sent to owner successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Summary Section -->
    <div class="summary-container">
        <div class="summary-card" style="border-top: 4px solid #3498db;">
            <div class="summary-number text-primary">৳ <?= number_format($totals['total_amount'] ?? 0, 2) ?></div>
            <div class="summary-label">Total Received</div>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #e74c3c;">
            <div class="summary-number text-danger">৳ <?= number_format($totals['total_commission'] ?? 0, 2) ?></div>
            <div class="summary-label">Your Commission (10%)</div>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #2ecc71;">
            <div class="summary-number text-success">৳ <?= number_format($totals['total_paid'] ?? 0, 2) ?></div>
            <div class="summary-label">Paid to Owners</div>
        </div>
        
        <div class="summary-card" style="border-top: 4px solid #f39c12;">
            <div class="summary-number text-warning">৳ <?= number_format($totals['total_pending'] ?? 0, 2) ?></div>
            <div class="summary-label">Pending Payment</div>
        </div>
    </div>
    
    <!-- Payment Breakdown -->
    <div class="mb-4 p-3" style="background: white; border-radius: 10px;">
        <h5><i class="fas fa-calculator"></i> Payment Breakdown</h5>
        <div class="row">
            <div class="col-md-3">
                <small class="text-muted">Total User Payments</small><br>
                <strong class="text-primary">৳ <?= number_format($totals['total_amount'] ?? 0, 2) ?></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted">- Your 10% Commission</small><br>
                <strong class="text-danger">-৳ <?= number_format($totals['total_commission'] ?? 0, 2) ?></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted">= Owners Should Get</small><br>
                <strong class="text-success">৳ <?= number_format($totals['total_owner_amount'] ?? 0, 2) ?></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Your Net Profit</small><br>
                <strong class="text-warning">৳ <?= number_format($totals['total_commission'] ?? 0, 2) ?></strong>
            </div>
        </div>
    </div>
    
    <!-- Payments Table -->
    <div class="table-container">
        <h5 class="mb-3"><i class="fas fa-list"></i> All User Payments</h5>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Owner</th>
                        <th>Hotel/Room</th>
                        <th class="amount-cell">Payment Details</th>
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
                                <small class="text-muted"><?= $row['user_email'] ?></small>
                            </td>
                            <td>
                                <strong><?= $row['owner_name'] ?></strong><br>
                                <small class="text-muted"><?= $row['owner_email'] ?></small>
                            </td>
                            <td><?= $row['hotel_name'] ?>
                                <?php if(!empty($row['room_title'])): ?>
                                    <br><small class="text-muted">Room: <?= $row['room_title'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="amount-cell">
                                <div class="text-success">
                                    <strong>৳ <?= number_format($row['amount'], 2) ?></strong>
                                </div>
                                <div class="text-danger" style="font-size: 12px;">
                                    - Commission: ৳<?= number_format($row['commission'], 2) ?>
                                </div>
                                <div class="text-info" style="font-size: 12px;">
                                    → Owner gets: ৳<?= number_format($row['owner_amount'], 2) ?>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <?= date('d M Y', strtotime($row['booking_date'])) ?>
                                </div>
                            </td>
                            <td>
                                <?php if($row['owner_paid_status'] == 'paid'): ?>
                                    <span class="badge badge-paid">
                                        <i class="fas fa-check"></i> Paid
                                    </span><br>
                                    <small class="text-muted">
                                        <?= date('d M', strtotime($row['owner_paid_date'])) ?>
                                    </small>
                                <?php else: ?>
                                    <span class="badge badge-pending">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('d M', strtotime($row['created_at'])) ?><br>
                                <small class="text-muted">
                                    <?= date('h:i A', strtotime($row['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <?php if($row['owner_paid_status'] == 'pending'): ?>
                                    <a href="?pay=<?= $row['id'] ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Send ৳<?= number_format($row['owner_amount'], 2) ?> to <?= $row['owner_name'] ?>?')">
                                        <i class="fas fa-paper-plane"></i> Pay
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-check"></i> Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-money-bill-wave fa-2x mb-3"></i><br>
                                No user payments found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Summary Info -->
    <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 10px;">
        <h6><i class="fas fa-info-circle"></i> How It Works:</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="p-2">
                    <strong>1. User Payment</strong><br>
                    User pays full amount → System receives 100%
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-2">
                    <strong>2. Commission</strong><br>
                    System keeps 10% → Owner gets 90%
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-2">
                    <strong>3. Owner Payment</strong><br>
                    Admin pays owner's 90% when ready
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>
</body>
</html>