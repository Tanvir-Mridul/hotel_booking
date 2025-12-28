<?php
session_start();
include "../db_connect.php";
include "sidebar.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle subscription actions
if(isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == 'approve') {
        mysqli_query($conn, "UPDATE owner_subscriptions SET status='approved' WHERE id='$id'");
    } elseif($action == 'reject') {
        mysqli_query($conn, "UPDATE owner_subscriptions SET status='rejected' WHERE id='$id'");
    } elseif($action == 'off') {
        mysqli_query($conn, "UPDATE owner_subscriptions SET status='off' WHERE id='$id'");
    } elseif($action == 'on') {
        mysqli_query($conn, "UPDATE owner_subscriptions SET status='approved' WHERE id='$id'");
    }

    header("Location: manage_subscriptions.php");
    exit();
}

// Fetch all subscriptions
$sql = "SELECT os.*, u.name AS owner_name, s.name AS package_name, s.duration_days
        FROM owner_subscriptions os
        JOIN users u ON os.owner_id = u.id
        JOIN subscriptions s ON os.subscription_id = s.id
        ORDER BY os.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subscriptions - Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; }
        .main { max-width: 1200px; margin: 30px auto; }
        .card { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .badge { font-size: 0.9rem; padding: 0.4em 0.7em; }
        .actions { display: flex; flex-direction: column; gap: 5px; }
        .status-pending { background: #ffc107; color: #212529; }
        .status-approved { background: #28a745; color: #fff; }
        .status-expired { background: #6c757d; color: #fff; }
        .status-off { background: #6c757d; color: #fff; }
        .status-rejected { background: #dc3545; color: #fff; }
    </style>
</head>
<body>

<div class="main">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-dollar-sign"></i> Manage Owner Subscriptions</h4>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Owner</th>
                            <th>Package</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row=mysqli_fetch_assoc($result)): ?>
                                <?php 
                                $status_class = 'status-pending';
                                if($row['status']=='approved') $status_class='status-approved';
                                if($row['status']=='expired') $status_class='status-expired';
                                if($row['status']=='off') $status_class='status-off';
                                if($row['status']=='rejected') $status_class='status-rejected';
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['owner_name'] ?></td>
                                    <td><?= $row['package_name'] ?></td>
                                    <td><?= $row['start_date'] ?> → <?= $row['end_date'] ?></td>
                                    <td><span class="badge <?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td class="actions">
                                        <?php if($row['status']=='pending'): ?>
                                            <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✅ Approve</a>
                                            <a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">❌ Reject</a>
                                        <?php elseif($row['status']=='approved'): ?>
                                            <a href="?action=off&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">🔴 Turn Off</a>
                                        <?php elseif($row['status']=='off'): ?>
                                            <a href="?action=on&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✅ Turn On</a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No subscriptions found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>
