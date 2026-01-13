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
$sql = "SELECT b.*, h.image, h.rooms, h.capacity 
        FROM bookings b
        LEFT JOIN hotels h ON b.hotel_id = h.id
        WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found!");
}

// Include header
include "../header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .payment-container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .hotel-image { width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 20px; }
        .price-box { background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; }
        .price-amount { font-size: 36px; color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container mt-4">
    <div class="payment-container">
        <h3 class="text-center mb-4"><i class="fas fa-credit-card"></i> Payment Checkout</h3>
        
        <!-- Booking Details -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5><?php echo htmlspecialchars($booking['hotel_name']); ?></h5>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo $booking['location']; ?></p>
                <p><i class="fas fa-calendar"></i> <?php echo $booking['booking_date']; ?></p>
                <p><i class="fas fa-bed"></i> Rooms: <?php echo $booking['rooms'] ?? 1; ?></p>
            </div>
            <div class="col-md-6">
                <div class="price-box">
                    <div class="price-amount">৳ <?php echo $booking['price']; ?></div>
                    <p>Total Amount</p>
                </div>
            </div>
        </div>
        
        <!-- Payment Form -->
        <form action="../ssl/user_payment_request.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $booking['price']; ?>">
            
            <div class="form-group">
                <label>Payment Method</label>
                <select class="form-control" disabled>
                    <option>SSLCommerz (Credit/Debit Card, Mobile Banking)</option>
                </select>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You'll be redirected to SSLCommerz secure payment page.
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-lock"></i> Pay Now - ৳ <?php echo $booking['price']; ?>
                </button>
                <a href="my_booking.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>