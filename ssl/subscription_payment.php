<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

// Validate package_id
if (!isset($_GET['package_id'])) {
    die("Invalid package");
}

$package_id = (int) $_GET['package_id'];

// Get package details
$pkg_q = mysqli_query($conn, "SELECT * FROM subscriptions WHERE id='$package_id'");
if (mysqli_num_rows($pkg_q) == 0) {
    die("Invalid package selected");
}

$package = mysqli_fetch_assoc($pkg_q);

$owner_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subscription Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f9; padding-top: 50px; }
        .payment-box { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .payment-box h3 { font-weight: 700; margin-bottom: 20px; }
        .payment-box p { font-size: 16px; }
        .btn-pay { border-radius: 30px; padding: 10px 25px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <div class="payment-box text-center">
        <h3><i class="fas fa-crown text-warning"></i> Confirm Subscription</h3>

        <p><strong>Package:</strong> <?= htmlspecialchars($package['name']); ?></p>
        <p><strong>Duration:</strong> <?= $package['duration_days']; ?> days</p>
        <p><strong>Amount:</strong> ৳ <?= $package['price']; ?></p>

        <form method="post" action="ssl_request.php">
            <input type="hidden" name="package_id" value="<?= $package_id; ?>">
            <input type="hidden" name="amount" value="<?= $package['price']; ?>">
            <button type="submit" class="btn btn-success btn-pay">
                <i class="fas fa-credit-card"></i> Pay Now
            </button>
        </form>

        <a href="/hotel_booking/owner/subscription.php" class="btn btn-secondary mt-3">Cancel</a>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

</body>
</html>
