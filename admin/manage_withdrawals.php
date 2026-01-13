<?php
session_start();
include "../db_connect.php";
include "sidebar.php";
include "../includes/notification_helper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle withdrawal actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $notes = $_GET['notes'] ?? '';
    
    $withdrawal_q = mysqli_query($conn, "SELECT * FROM owner_withdrawals WHERE id='$id'");
    $withdrawal = mysqli_fetch_assoc($withdrawal_q);
    
    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE owner_withdrawals SET status='approved', admin_notes='$notes' WHERE id='$id'");
        
        sendNotification($withdrawal['owner_id'], 'owner',
            "✅ Withdrawal Approved!\nAmount: ৳{$withdrawal['amount']}\nStatus: Approved\nNotes: $notes",
            "/hotel_booking/owner/finance.php"
        );
        
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE owner_withdrawals SET status='rejected', admin_notes='$notes' WHERE id='$id'");
        
        sendNotification($withdrawal['owner_id'], 'owner',
            "❌ Withdrawal Rejected!\nAmount: ৳{$withdrawal['amount']}\nStatus: Rejected\nNotes: $notes",
            "/hotel_booking/owner/finance.php"
        );
        
    } elseif ($action == 'mark_paid') {
        mysqli_query($conn, "UPDATE owner_withdrawals SET status='paid', admin_notes='$notes', processed_at=NOW() WHERE id='$id'");
        
        // Update owner_payments summary
        mysqli_query($conn, "
            INSERT INTO owner_payments (owner_id, total_paid, pending_balance)
            VALUES ('{$withdrawal['owner_id']}', '{$withdrawal['amount']}', -{$withdrawal['amount']})
            ON DUPLICATE KEY UPDATE 
            total_paid = total_paid + {$withdrawal['amount']},
            last_updated = NOW()
        ");
        
        sendNotification($withdrawal['owner_id'], 'owner',
            "💰 Payment Sent!\nAmount ৳{$withdrawal['amount']} has been sent to your account.\nNotes: $notes",
            "/hotel_booking/owner/finance.php"
        );
    }
    
    header("Location: manage_withdrawals.php");
    exit();
}

// Fetch all withdrawal requests
$sql = "SELECT w.*, u.name as owner_name, op.total_earned, op.total_paid
        FROM owner_withdrawals w
        JOIN users u ON w.owner_id = u.id
        LEFT JOIN owner_payments op ON w.owner_id = op.owner_id
        ORDER BY 
        CASE 
            WHEN w.status = 'pending' THEN 1
            WHEN w.status = 'approved' THEN 2
            ELSE 3
        END, w.requested_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Withdrawals</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; }
        .card { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .badge-pending { background: #ffc107; color: #212529; }
        .badge-approved { background: #28a745; color: white; }
        .badge-paid { background: #17a2b8; color: white; }
        .badge-rejected { background: #dc3545; color: white; }
        .notes-input { width: 300px; }
        .owner-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Owner Withdrawal Requests</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Owner</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Account Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <strong><?= $row['owner_name'] ?></strong><br>
                                    <small class="text-muted">
                                        Earned: ৳<?= number_format($row['total_earned'] ?? 0, 2) ?><br>
                                        Paid: ৳<?= number_format($row['total_paid'] ?? 0, 2) ?>
                                    </small>
                                </td>
                                <td class="font-weight-bold">৳ <?= number_format($row['amount'], 2) ?></td>
                                <td><?= ucfirst($row['method']) ?></td>
                                <td><small><?= nl2br(htmlspecialchars($row['account_details'])) ?></small></td>
                                <td>
                                    <?php 
                                    $badge_class = 'badge-pending';
                                    if($row['status'] == 'approved') $badge_class = 'badge-approved';
                                    if($row['status'] == 'paid') $badge_class = 'badge-paid';
                                    if($row['status'] == 'rejected') $badge_class = 'badge-rejected';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
                                </td>
                                <td><?= date('d M Y', strtotime($row['requested_at'])) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if($row['status'] == 'pending'): ?>
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="showActionModal('approve', <?= $row['id'] ?>, '<?= $row['owner_name'] ?>', <?= $row['amount'] ?>)">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="showActionModal('reject', <?= $row['id'] ?>, '<?= $row['owner_name'] ?>', <?= $row['amount'] ?>)">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        <?php elseif($row['status'] == 'approved'): ?>
                                            <button class="btn btn-info btn-sm" 
                                                    onclick="showActionModal('mark_paid', <?= $row['id'] ?>, '<?= $row['owner_name'] ?>', <?= $row['amount'] ?>)">
                                                <i class="fas fa-money-bill"></i> Mark Paid
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No withdrawal requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="actionForm">
                <input type="hidden" name="action" id="modalAction">
                <input type="hidden" name="id" id="modalId">
                
                <div class="modal-body">
                    <p id="modalMessage"></p>
                    <div class="form-group">
                        <label>Admin Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add notes..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showActionModal(action, id, ownerName, amount) {
    const modal = $('#actionModal');
    const form = $('#actionForm')[0];
    
    // Set form action
    form.action = `?action=${action}&id=${id}`;
    $('#modalAction').val(action);
    $('#modalId').val(id);
    
    // Set modal content based on action
    let title = '', message = '', btnText = '', btnClass = '';
    
    if (action === 'approve') {
        title = 'Approve Withdrawal';
        message = `Approve withdrawal of ৳${amount} to ${ownerName}?`;
        btnText = 'Approve';
        btnClass = 'btn-success';
    } else if (action === 'reject') {
        title = 'Reject Withdrawal';
        message = `Reject withdrawal request of ৳${amount} from ${ownerName}?`;
        btnText = 'Reject';
        btnClass = 'btn-danger';
    } else if (action === 'mark_paid') {
        title = 'Mark as Paid';
        message = `Mark withdrawal of ৳${amount} to ${ownerName} as paid?`;
        btnText = 'Mark Paid';
        btnClass = 'btn-info';
    }
    
    $('#modalTitle').text(title);
    $('#modalMessage').text(message);
    $('#modalSubmitBtn').text(btnText).removeClass().addClass('btn ' + btnClass);
    
    modal.modal('show');
}

// Handle form submission
$('#actionForm').on('submit', function(e) {
    e.preventDefault();
    
    const action = $('#modalAction').val();
    const id = $('#modalId').val();
    const notes = encodeURIComponent($('textarea[name="notes"]').val());
    
    // Redirect with parameters
    window.location.href = `?action=${action}&id=${id}&notes=${notes}`;
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>