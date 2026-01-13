<?php
session_start();
include "../db_connect.php";
include "sidebar.php";
include "../includes/notification_helper.php";

mysqli_query($conn, "UPDATE owner_subscriptions 
    SET status='expired'
    WHERE status='approved' AND end_date < CURDATE()
");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle subscription actions
if (isset($_GET['action'], $_GET['id'])) {

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    // get package duration
    $pkg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.duration_days, os.owner_id, s.price
        FROM owner_subscriptions os
        JOIN subscriptions s ON os.package_id = s.id
        WHERE os.id='$id'
    "));

    $days = (int) $pkg['duration_days'];
    $owner_id = $pkg['owner_id'];
    $package_price = $pkg['price'];

    if ($action == 'approve') {

        mysqli_query($conn, "UPDATE owner_subscriptions 
            SET 
                status='approved',
                start_date=CURDATE(),
                end_date=DATE_ADD(CURDATE(), INTERVAL $days DAY)
            WHERE id='$id'
        ");

        // 🔔 Notify Owner
        sendNotification($owner_id, 'owner',
            "✅ Your subscription (৳$package_price) has been approved! Premium features activated.",
            "/hotel_booking/owner/dashboard.php"
        );

    } elseif ($action == 'off' || $action == 'expire') {

        // subscription off
        mysqli_query($conn, "UPDATE owner_subscriptions 
            SET status='expired' 
            WHERE id='$id'
        ");

        // owner id বের করো
        $q = mysqli_query($conn,"SELECT owner_id FROM owner_subscriptions WHERE id='$id'");
        $row = mysqli_fetch_assoc($q);
        $owner_id = $row['owner_id'];

        // owner's hotels automatically off
        mysqli_query($conn,"UPDATE hotels 
            SET status='off' 
            WHERE owner_id='$owner_id'
        ");
        
        // 🔔 Notify Owner
        sendNotification($owner_id, 'owner',
            "⚠️ Your subscription (৳$package_price) has been deactivated. Your hotels are now offline.",
            "/hotel_booking/owner/subscription.php"
        );
        
    } elseif($action == 'on') {
        // subscription on
        mysqli_query($conn, "UPDATE owner_subscriptions 
            SET status='approved' 
            WHERE id='$id'
        ");

        // owner id বের করো
        $q = mysqli_query($conn,"SELECT owner_id FROM owner_subscriptions WHERE id='$id'");
        $row = mysqli_fetch_assoc($q);
        $owner_id = $row['owner_id'];

        // hotels on
        mysqli_query($conn, "UPDATE hotels
            SET status='approved'
            WHERE owner_id='$owner_id'
            AND status='off'
        ");
        
        // 🔔 Notify Owner
        sendNotification($owner_id, 'owner',
            "✅ Your subscription (৳$package_price) has been reactivated. Hotels are now online.",
            "/hotel_booking/owner/dashboard.php"
        );
    }

    header("Location: manage_subscriptions.php");
    exit();
}

// Fetch all subscriptions
$sql = "SELECT 
    os.*,
    u.name AS owner_name,
    u.email,
    s.name AS package_name,
    s.price AS package_price,
    s.duration_days
FROM owner_subscriptions os
JOIN users u ON os.owner_id = u.id
JOIN subscriptions s ON os.package_id = s.id
ORDER BY os.id DESC
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Subscriptions - Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
        }

        .main {
            max-width: 1300px;
            margin: 30px auto;
        }

        .card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.7em;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .status-pending {
            background: #ffc107;
            color: #212529;
        }

        .status-approved {
            background: #28a745;
            color: #fff;
        }

        .status-expired {
            background: #6c757d;
            color: #fff;
        }

        .status-off {
            background: #6c757d;
            color: #fff;
        }

        .status-rejected {
            background: #dc3545;
            color: #fff;
        }
        
        .price-badge {
            background: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .amount-column {
            text-align: center;
            font-weight: bold;
        }
        
        .money-icon {
            color: #28a745;
            margin-right: 5px;
        }
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
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <?php
                                    $status_class = 'status-pending';
                                    if ($row['status'] == 'approved')
                                        $status_class = 'status-approved';
                                    if ($row['status'] == 'expired')
                                        $status_class = 'status-expired';
                                    if ($row['status'] == 'off')
                                        $status_class = 'status-off';
                                    if ($row['status'] == 'rejected')
                                        $status_class = 'status-rejected';
                                    
                                    // Format price
                                    $price = $row['package_price'];
                                    $formatted_price = "৳ " . number_format($price, 2);
                                    ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td>
                                            <strong><?= $row['owner_name'] ?></strong><br>
                                            <small class="text-muted"><?= $row['email'] ?></small>
                                        </td>
                                        <td>
                                            <strong><?= $row['package_name'] ?></strong><br>
                                            <small><?= $row['duration_days'] ?> days</small>
                                        </td>
                                        <td>
                                            <?= $row['start_date'] ? date('d M Y', strtotime($row['start_date'])) : 'N/A' ?>
                                            <br>→<br>
                                            <?= $row['end_date'] ? date('d M Y', strtotime($row['end_date'])) : 'N/A' ?>
                                        </td>
                                        <td class="amount-column">
                                            <span class="price-badge">
                                                <i class="fas fa-money-bill-wave money-icon"></i>
                                                <?= $formatted_price ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $status_class ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <a href="?action=approve&id=<?= $row['id'] ?>" 
                                                   class="btn btn-success btn-sm mb-1"
                                                   onclick="return confirm('Approve subscription for <?= $formatted_price ?>?')">
                                                    ✅ Approve
                                                </a>
                                                <a href="?action=reject&id=<?= $row['id'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Reject this subscription?')">
                                                    ❌ Reject
                                                </a>

                                            <?php elseif ($row['status'] == 'approved'): ?>
                                                <a href="?action=off&id=<?= $row['id'] ?>" 
                                                   class="btn btn-warning btn-sm"
                                                   onclick="return confirm('Deactivate this subscription?')">
                                                    🔴 Deactivate
                                                </a>
                                                <small class="text-muted mt-1">
                                                    Expires: <?= date('d M Y', strtotime($row['end_date'])) ?>
                                                </small>

                                            <?php elseif ($row['status'] == 'expired'): ?>
                                                <a href="?action=on&id=<?= $row['id'] ?>" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('Reactivate subscription for <?= $formatted_price ?>?')">
                                                    🟢 Renew
                                                </a>
                                                <small class="text-muted mt-1">
                                                    Expired
                                                </small>

                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No subscriptions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <?php 
                            // Calculate total amount
                            $total_sql = "SELECT SUM(s.price) as total_amount, COUNT(*) as total_count 
                                        FROM owner_subscriptions os
                                        JOIN subscriptions s ON os.package_id = s.id
                                        WHERE os.status != 'rejected'";
                            $total_result = mysqli_fetch_assoc(mysqli_query($conn, $total_sql));
                            ?>
                            <tr class="bg-light">
                                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                <td class="amount-column">
                                    <span class="price-badge" style="background: #343a40;">
                                        <i class="fas fa-coins"></i>
                                        ৳ <?= number_format($total_result['total_amount'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-dark">
                                        <?= $total_result['total_count'] ?> Subscriptions
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h6>Total Revenue</h6>
                                <h3>৳ <?= number_format($total_result['total_amount'], 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
                    // Count by status
                    $status_sql = "SELECT 
                        SUM(CASE WHEN os.status = 'approved' THEN s.price ELSE 0 END) as active_revenue,
                        SUM(CASE WHEN os.status = 'pending' THEN s.price ELSE 0 END) as pending_revenue,
                        COUNT(CASE WHEN os.status = 'approved' THEN 1 END) as active_count,
                        COUNT(CASE WHEN os.status = 'pending' THEN 1 END) as pending_count
                        FROM owner_subscriptions os
                        JOIN subscriptions s ON os.package_id = s.id";
                    $status_result = mysqli_fetch_assoc(mysqli_query($conn, $status_sql));
                    ?>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h6>Active Subscriptions</h6>
                                <h3><?= $status_result['active_count'] ?></h3>
                                <small>৳ <?= number_format($status_result['active_revenue'], 2) ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h6>Pending Subscriptions</h6>
                                <h3><?= $status_result['pending_count'] ?></h3>
                                <small>৳ <?= number_format($status_result['pending_revenue'], 2) ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h6>Monthly Revenue</h6>
                                <?php 
                                $monthly_sql = "SELECT SUM(s.price) as monthly_revenue
                                              FROM owner_subscriptions os
                                              JOIN subscriptions s ON os.package_id = s.id
                                              WHERE MONTH(os.start_date) = MONTH(CURDATE())
                                              AND YEAR(os.start_date) = YEAR(CURDATE())";
                                $monthly_result = mysqli_fetch_assoc(mysqli_query($conn, $monthly_sql));
                                ?>
                                <h3>৳ <?= number_format($monthly_result['monthly_revenue'] ?? 0, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>