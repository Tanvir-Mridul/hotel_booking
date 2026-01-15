///receipt.php/
<?php
session_start();
include "../db_connect.php";

// Debug
error_log("=== RECEIPT PAGE ACCESS ===");
error_log("GET Data: " . print_r($_GET, true));
error_log("SESSION Data: " . print_r($_SESSION, true));

if (!isset($_GET['receipt_id'])) {
    die("No receipt ID provided");
}

$receipt_id = mysqli_real_escape_string($conn, $_GET['receipt_id']);
error_log("Looking for receipt: $receipt_id");

// First check in user_payments
$sql = "SELECT * FROM user_payments WHERE receipt_id = '$receipt_id'";
error_log("Query: $sql");

$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Query failed: " . mysqli_error($conn));
    die("Database error");
}

if (mysqli_num_rows($result) == 0) {
    // Check in bookings as fallback
    error_log("Not found in user_payments. Checking bookings...");
    
    // Try to find by booking ID if receipt_id is actually booking_id
    $sql2 = "SELECT 
                b.*, 
                u.name as user_name,
                h.hotel_name,
                '' as receipt_id,
                b.price as amount,
                'success' as payment_status,
                b.booking_date
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN hotels h ON b.hotel_id = h.id
            WHERE b.id = '$receipt_id' 
            OR b.id = (SELECT booking_id FROM user_payments WHERE receipt_id LIKE '%$receipt_id%' LIMIT 1)";
    
    error_log("Fallback query: $sql2");
    $result = mysqli_query($conn, $sql2);
    
    if (mysqli_num_rows($result) == 0) {
        error_log("Receipt not found anywhere");
        
        // Show debug info
        echo "<h3>Debug Info:</h3>";
        echo "Receipt ID: $receipt_id<br>";
        echo "Session User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
        
        // Show recent payments
        echo "<h4>Recent payments in database:</h4>";
        $recent = mysqli_query($conn, "SELECT receipt_id, booking_id, amount FROM user_payments ORDER BY id DESC LIMIT 5");
        while ($row = mysqli_fetch_assoc($recent)) {
            echo "Receipt: " . $row['receipt_id'] . " | Booking: " . $row['booking_id'] . " | Amount: " . $row['amount'] . "<br>";
        }
        
        die("<h2>Receipt not found!</h2><p>Please contact support with Receipt ID: $receipt_id</p>");
    } else {
        error_log("Found in bookings table");
    }
}

$payment = mysqli_fetch_assoc($result);
error_log("Payment data found: " . print_r($payment, true));

// Verify user access
$current_user_id = $_SESSION['user_id'] ?? 0;
if ($payment['user_id'] != $current_user_id && $_SESSION['role'] != 'admin') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .receipt-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-id {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .amount-box {
            background: #28a745;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .status-badge {
            font-size: 16px;
            padding: 8px 15px;
        }
        .print-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt-container">
            <div class="receipt-header">
                <div class="row">
                    <div class="col-6">
                        <h2><i class="fas fa-hotel"></i> Hotel Booking</h2>
                        <p class="text-muted">Payment Receipt</p>
                    </div>
                    <div class="col-6 text-right">
                        <div class="receipt-id">
                            <?php echo $payment['receipt_id'] ?? 'N/A'; ?>
                        </div>
                        <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($payment['booking_date'])); ?></small>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Booking Details</h5>
                    <p><strong>Hotel:</strong> <?php echo $payment['hotel_name']; ?></p>
                    <?php if(!empty($payment['room_title'])): ?>
                        <p><strong>Room:</strong> <?php echo $payment['room_title']; ?></p>
                    <?php endif; ?>
                    <p><strong>Booking ID:</strong> #<?php echo $payment['booking_id']; ?></p>
                    <p><strong>Transaction ID:</strong> <?php echo $payment['tran_id']; ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Payment Status</h5>
                    <?php
                    $status_class = ($payment['payment_status'] == 'success') ? 'success' : 'warning';
                    ?>
                    <span class="badge badge-<?php echo $status_class; ?> status-badge">
                        <?php echo strtoupper($payment['payment_status']); ?>
                    </span>
                    
                    <div class="mt-3">
                        <p><strong>Payment Date:</strong><br>
                        <?php echo date('F j, Y', strtotime($payment['booking_date'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="amount-box">
                ৳ <?php echo number_format($payment['amount'], 2); ?>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Commission Breakdown</h5>
                    <table class="table table-sm">
                        <tr>
                            <td>Total Amount:</td>
                            <td class="text-right">৳ <?php echo number_format($payment['amount'], 2); ?></td>
                        </tr>
                        
                        <tr class="table-success">
                            <td><strong>Owner Receives:</strong></td>
                            <td class="text-right"><strong>৳ <?php echo number_format($payment['amount'], 2); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button onclick="window.print()" class="btn btn-primary print-btn">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <a href="my_booking.php" class="btn btn-secondary print-btn">
                    <i class="fas fa-calendar-alt"></i> My Bookings
                </a>
                <a href="../hotel/hotel_list.php" class="btn btn-success print-btn">
                    <i class="fas fa-search"></i> Book Again
                </a>
            </div>

            <div class="mt-4 text-center text-muted">
                <small>
                    <i class="fas fa-info-circle"></i>
                    This is an automated receipt. For any queries, contact support.
                </small>
            </div>
        </div>
    </div>

    <script>
        // Auto print option (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // }
    </script>
</body>
</html>