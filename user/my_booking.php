/my_booking.php/
<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ❌❌ Cancel Booking (SECURE) ❌❌ */
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    
    // Get booking info first - UPDATED FOR ROOM
    $booking_q = mysqli_query($conn, 
        "SELECT b.hotel_name, b.room_title, b.owner_id, b.room_id 
         FROM bookings b 
         WHERE b.id=$cancel_id AND b.user_id=$user_id"
    );
    $booking_info = mysqli_fetch_assoc($booking_q);
    
    if ($booking_info) {
        // Update booking status
        mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id=$cancel_id");
        
        // Update room status if room booking
        if (!empty($booking_info['room_id'])) {
            mysqli_query($conn, "UPDATE rooms SET status='available' WHERE id='{$booking_info['room_id']}'");
        }
        
        // Include notification helper
        include "../includes/notification_helper.php";
        
        // Send notification to owner
        $item_name = !empty($booking_info['room_title']) ? $booking_info['room_title'] : $booking_info['hotel_name'];
        
        sendNotification($booking_info['owner_id'], 'owner',
            "❌ Booking cancelled for \"$item_name\" by " . $_SESSION['name'],
            "/hotel_booking/owner/manage_bookings.php"
        );
        
        header("Location: my_booking.php?msg=cancelled");
        exit();
    }
}

/* ✅ Booking List (UPDATED FOR ROOM SYSTEM) */
$sql = "SELECT 
    b.*, 
    h.location,
    h.owner_id,
    r.room_title,
    r.capacity as room_capacity,
    r.room_count as max_rooms
FROM bookings b
LEFT JOIN hotels h ON b.hotel_id = h.id
LEFT JOIN rooms r ON b.room_id = r.id
WHERE b.user_id = $user_id
ORDER BY 
    CASE 
        WHEN b.status = 'pending' THEN 1
        WHEN b.status = 'confirmed' THEN 2
        WHEN b.status = 'cancelled' THEN 3
        ELSE 4
    END,
    b.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .booking-card { 
            background: #fff; 
            padding: 25px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #3498db;
        }
        .booking-card.cancelled { border-left-color: #e74c3c; }
        .booking-card.confirmed { border-left-color: #2ecc71; }
        .booking-card.pending { border-left-color: #f39c12; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; }
        .badge-pending { background: #f39c12; color: white; }
        .badge-confirmed { background: #2ecc71; color: white; }
        .badge-cancelled { background: #e74c3c; color: white; }
        
        .room-badge { background: #3498db; color: white; padding: 4px 10px; border-radius: 15px; font-size: 11px; }
        .date-badge { background: #9b59b6; color: white; padding: 4px 10px; border-radius: 15px; font-size: 11px; }
        
        .btn-action { margin: 2px; font-size: 13px; }
    </style>
    <?php include "../header.php"; ?>
</head>

<body>

<?php include "../header.php"; ?>

<div class="container">
    <h3 class="mb-4"><i class="fas fa-calendar-alt"></i> My Bookings</h3>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'cancelled'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Booking cancelled successfully!
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'paid'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Payment successful! Check your receipt.
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($booking = mysqli_fetch_assoc($result)): 
            // Determine card class
            $card_class = 'booking-card ' . $booking['status'];
            
            // Determine badge class
            $badge_class = 'badge-' . $booking['status'];
        ?>
        
            <div class="<?php echo $card_class; ?>">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Hotel/Room Name -->
                        <h5 class="mb-2">
                            <?php 
                            if (!empty($booking['room_title'])) {
                                echo '<i class="fas fa-bed text-primary"></i> ' . htmlspecialchars($booking['room_title']);
                            } else {
                                echo '<i class="fas fa-hotel text-primary"></i> ' . htmlspecialchars($booking['hotel_name']);
                            }
                            ?>
                        </h5>
                        
                        <!-- Location -->
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($booking['location']); ?>
                        </p>
                        
                        <!-- Dates -->
                        <div class="mb-2">
                            <?php if (isset($booking['check_in_date']) && isset($booking['check_out_date'])): ?>
                                <span class="date-badge">
                                    <i class="fas fa-calendar-check"></i>
                                    <?php echo date("d M", strtotime($booking['check_in_date'])); ?> - 
                                    <?php echo date("d M Y", strtotime($booking['check_out_date'])); ?>
                                </span>
                                
                                <?php 
                                // Calculate nights
                                $nights = (strtotime($booking['check_out_date']) - strtotime($booking['check_in_date'])) / (60 * 60 * 24);
                                $nights = max(1, $nights);
                                ?>
                                <span class="badge badge-light">
                                    <?php echo $nights; ?> Night(s)
                                </span>
                            <?php else: ?>
                                <span class="date-badge">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date("d M Y", strtotime($booking['booking_date'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Room Details -->
                        <?php if (!empty($booking['room_title'])): ?>
                        <div class="mb-2">
                            <span class="room-badge">
                                <i class="fas fa-door-closed"></i>
                                Rooms: <?php echo $booking['rooms_count'] ?? 1; ?>
                            </span>
                            
                            <span class="room-badge" style="background: #e74c3c;">
                                <i class="fas fa-users"></i>
                                Guests: <?php echo $booking['guests'] ?? 1; ?>
                            </span>
                            
                            <?php if (!empty($booking['room_capacity'])): ?>
                            <span class="badge badge-light">
                                Capacity: <?php echo $booking['room_capacity']; ?> Persons
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Price -->
                        <h5 class="text-success mt-3">
                            <i class="fas fa-tag"></i> 
                            ৳ <?php echo number_format($booking['price'], 2); ?>
                            <?php if (isset($nights)): ?>
                                <small class="text-muted">(৳ <?php echo number_format($booking['price'] / $nights, 2); ?>/night)</small>
                            <?php endif; ?>
                        </h5>
                        
                        <!-- Booking ID -->
                        <small class="text-muted">
                            <i class="fas fa-hashtag"></i> Booking ID: #<?php echo $booking['id']; ?>
                        </small>
                    </div>
                       
                    <div class="col-md-4 text-right">
                        <!-- Status Badge -->
                        <span class="badge <?php echo $badge_class; ?> mb-2">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                        
                        <div class="mt-3">
                            <!-- Action Buttons -->
                            <?php if ($booking['status'] !== 'cancelled'): ?>
                                
                                <!-- PAYMENT BUTTON FOR PENDING STATUS -->
                                <?php if($booking['status'] == 'initiated'): ?>
                                    <a href="payment_checkout.php?booking_id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-success btn-sm btn-action">
                                       <i class="fas fa-credit-card"></i> Pay Now
                                    </a>
                                <?php endif; ?>
                                
                                <!-- VIEW RECEIPT FOR CONFIRMED STATUS -->
                                <?php if($booking['status'] == 'confirmed'): 
                                    // Check if payment exists
                                    $payment_q = mysqli_query($conn, 
                                        "SELECT receipt_id FROM user_payments WHERE booking_id='{$booking['id']}' LIMIT 1"
                                    );
                                    if (mysqli_num_rows($payment_q) > 0) {
                                        $payment = mysqli_fetch_assoc($payment_q);
                                    ?>
                                        <a href="receipt.php?receipt_id=<?php echo $payment['receipt_id']; ?>" 
                                           class="btn btn-info btn-sm btn-action">
                                           <i class="fas fa-receipt"></i> View Receipt
                                        </a>
                                    <?php } ?>
                                <?php endif; ?>
                                
                                <!-- CANCEL BUTTON -->
                                <a href="?cancel_id=<?php echo $booking['id']; ?>"
                                   onclick="return confirm('Are you sure you want to cancel this booking?')"
                                   class="btn btn-danger btn-sm btn-action">
                                   <i class="fas fa-times"></i> Cancel
                                </a>
                                
                                <!-- CHAT BUTTON -->
                                <?php if(isset($booking['owner_id']) && $booking['owner_id'] > 0): ?>
                                <a href="../chat/chat.php?owner_id=<?= $booking['owner_id'] ?>" 
                                   class="btn btn-primary btn-sm btn-action">
                                   <i class="fas fa-comment"></i> Chat
                                </a>
                                <?php endif; ?>

                            <?php else: ?>
                                <span class="text-muted">Booking cancelled</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- View Details Link -->
                        <?php if (!empty($booking['room_id'])): ?>
                        <div class="mt-2">
                            <a href="../hotel/room_details.php?room_id=<?php echo $booking['room_id']; ?>" 
                               class="btn btn-outline-primary btn-sm">
                               <i class="fas fa-eye"></i> View Room
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
        
        <!-- Booking Stats -->
        <?php 
        $stats_sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(price) as total_spent
            FROM bookings WHERE user_id = $user_id";
        
        $stats_result = mysqli_query($conn, $stats_sql);
        $stats = mysqli_fetch_assoc($stats_result);
        ?>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Booking Summary</h5>
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3><?php echo $stats['total']; ?></h3>
                        <small class="text-muted">Total Bookings</small>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-success"><?php echo $stats['confirmed']; ?></h3>
                        <small class="text-muted">Confirmed</small>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-warning"><?php echo $stats['pending']; ?></h3>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-danger">৳ <?php echo number_format($stats['total_spent'], 2); ?></h3>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div class="text-center py-5">
            <div style="font-size: 80px; color: #ddd; margin-bottom: 20px;">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h4>No Bookings Yet</h4>
            <p class="text-muted mb-4">You haven't made any bookings yet. Start exploring hotels and rooms!</p>
            <a href="../hotel/hotel_list.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search"></i> Browse Hotels & Rooms
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>

</body>
</html>