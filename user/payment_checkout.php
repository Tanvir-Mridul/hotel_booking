<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    header("Location: my_booking.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

// Fetch booking details 
$sql = "SELECT b.*, 
               r.room_title, r.capacity, r.room_count,
               r.price_per_night, r.discount_price,
               h.image as hotel_image, h.owner_id
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        LEFT JOIN hotels h ON b.hotel_id = h.id
        WHERE b.id = ? 
        AND b.user_id = ? 
        AND b.status = 'initiated'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found or already confirmed!");
}


include "../header.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Payment Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            padding-top: 20px;
        }

        .payment-container {
            max-width: 700px;
            margin: auto;
        }

        .payment-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .booking-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .price-box {
            background: #27ae60;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .price-amount {
            font-size: 36px;
            font-weight: bold;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>

    <?php include "../header.php"; ?>

    <div class="container payment-container mt-4">
        <div class="payment-card">
            <h3 class="text-center mb-4"><i class="fas fa-credit-card"></i> Payment Checkout</h3>

            <!-- Booking Details -->
            <div class="booking-summary">
                <h5>Booking Summary</h5>

                <div class="detail-row">
                    <span>Room:</span>
                    <span><strong><?php echo htmlspecialchars($booking['room_title'] ?? $booking['hotel_name']); ?></strong></span>
                </div>

                <div class="detail-row">
                    <span>Hotel:</span>
                    <span><?php echo $booking['hotel_name']; ?></span>
                </div>

                <div class="detail-row">
                    <span>Check-in:</span>
                    <span><?php echo date('d M Y', strtotime($booking['check_in_date'])); ?></span>
                </div>

                <div class="detail-row">
                    <span>Check-out:</span>
                    <span><?php echo date('d M Y', strtotime($booking['check_out_date'])); ?></span>
                </div>

                <div class="detail-row">
                    <span>Nights:</span>
                    <span>
                        <?php
                        $nights = (strtotime($booking['check_out_date']) - strtotime($booking['check_in_date'])) / (60 * 60 * 24);
                        echo max(1, $nights);
                        ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span>Rooms:</span>
                    <span><?php echo $booking['rooms_count']; ?></span>
                </div>

                <div class="detail-row">
                    <span>Guests:</span>
                    <span><?php echo $booking['guests']; ?> Person(s)</span>
                </div>
            </div>

            <!-- Price Details -->
            <div class="price-box mb-4">
                <div class="price-amount">৳ <?php echo number_format($booking['price'], 2); ?></div>
                <p>Total Amount</p>
            </div>

            <!-- Payment Form -->
            <form action="../ssl/user_payment_request.php" method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                <input type="hidden" name="amount" value="<?php echo $booking['price']; ?>">
                <input type="hidden" name="owner_id" value="<?php echo $booking['owner_id']; ?>">

                <div class="form-group">
                    <label>Payment Method</label>
                    <select class="form-control" disabled>
                        <option>SSLCommerz (Credit/Debit Card, Mobile Banking)</option>
                    </select>
                    <small class="text-muted">Secure payment via SSLCommerz</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Important:</strong> You'll be redirected to SSLCommerz secure payment page.
                    After payment, you'll receive a confirmation receipt.
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-lock"></i> Pay Now - ৳ <?php echo number_format($booking['price'], 2); ?>
                    </button>
                    <a href="my_booking.php" class="btn btn-secondary ml-2">Cancel</a>
                </div>
            </form>

            <!-- Payment Info -->
            <div class="mt-4 pt-3 border-top">
                <h6><i class="fas fa-shield-alt"></i> Payment Security</h6>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div style="font-size: 30px; color: #3498db;">🔒</div>
                        <small>SSL Secure</small>
                    </div>
                    <div class="col-md-3">
                        <div style="font-size: 30px; color: #27ae60;">✓</div>
                        <small>Verified</small>
                    </div>
                    <div class="col-md-3">
                        <div style="font-size: 30px; color: #e74c3c;">🛡️</div>
                        <small>Protected</small>
                    </div>
                    <div class="col-md-3">
                        <div style="font-size: 30px; color: #9b59b6;">💳</div>
                        <small>Multiple Options</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>