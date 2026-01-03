<?php
session_start();
include "../db_connect.php";
include "sidebar.php";

$id = intval($_GET['id']);
$pkg = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM subscriptions WHERE id='$id'"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $days = $_POST['duration_days'];

    mysqli_query($conn,"
        UPDATE subscriptions SET
        name='$name',
        price='$price',
        duration_days='$days'
        WHERE id='$id'
    ");

    header("Location: manage_packages.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Package</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="main">
    <div class="card">
        <div class="card-header bg-primary text-white">✏ Edit Subscription Package</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>Plan Name</label>
                    <input type="text" name="name" value="<?= $pkg['name'] ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Price (৳)</label>
                    <input type="number" name="price" value="<?= $pkg['price'] ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Duration (Days)</label>
                    <input type="number" name="duration_days" value="<?= $pkg['duration_days'] ?>" class="form-control" required>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="manage_packages.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
