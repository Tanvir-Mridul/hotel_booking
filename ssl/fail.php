<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow text-center">
        <div class="card-body">
            <h3 class="text-danger mb-3">❌ Payment Failed</h3>
            <p>Your subscription payment was not successful.</p>
            <a href="/hotel_booking/owner/subscription.php" class="btn btn-primary">
                Try Again
            </a>
        </div>
    </div>
</div>

</body>
</html>
