<?php
session_start();
include "../db_connect.php";
include "sidebar.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $days = $_POST['duration_days'];

    mysqli_query($conn, "
        INSERT INTO subscriptions (name, price, duration_days)
        VALUES ('$name','$price','$days')
    ");

    header("Location: manage_packages.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Package</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="main">
        <div class="card">
            <div class="card-header bg-success text-white">➕ Add Subscription Package</div>
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label>Plan Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Price (৳)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Duration (Days)</label>
                        <input type="number" name="duration_days" class="form-control" required>
                    </div>

                    <button class="btn btn-success">Save Package</button>
                    <a href="manage_packages.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>

</body>

</html>