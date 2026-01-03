<?php
session_start();
include "../db_connect.php";
include "sidebar.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ===== DELETE PACKAGE ===== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM subscriptions WHERE id='$id'");
    header("Location: manage_packages.php");
    exit();
}

$packages = mysqli_query($conn, "SELECT * FROM subscriptions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Subscription Packages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body{
            background:#f4f6f9;
        }
        .main{
            margin-left:220px; /* sidebar width adjust */
            padding:25px;
        }
        .card{
            border-radius:12px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
            border:none;
        }
        .card-header{
            border-radius:12px 12px 0 0;
        }
        table th, table td{
            vertical-align:middle!important;
        }
        .badge-days{
            background:#e3f2fd;
            color:#0d6efd;
            padding:6px 10px;
            border-radius:20px;
            font-size:13px;
        }
        .btn-sm{
            padding:5px 10px;
        }
        .table-hover tbody tr:hover{
            background:#f1f7ff;
        }
    </style>
</head>

<body>

<div class="main">

    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold mb-0">📦 Subscription Packages</h4>
        <a href="add_package.php" class="btn btn-success">
            ➕ Add New Package
        </a>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Plan Name</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($packages)>0): ?>
                        <?php while($p = mysqli_fetch_assoc($packages)): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="text-success font-weight-bold">৳ <?= $p['price'] ?></td>
                            <td>
                                <span class="badge-days"><?= $p['duration_days'] ?> Days</span>
                            </td>
                            <td>
                                <a href="edit_package.php?id=<?= $p['id'] ?>" 
                                   class="btn btn-primary btn-sm">
                                   ✏ Edit
                                </a>
                                <a href="?delete=<?= $p['id'] ?>" 
                                   onclick="return confirm('Are you sure to delete this package?')"
                                   class="btn btn-danger btn-sm">
                                   🗑 Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-muted">No packages found</td>
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
