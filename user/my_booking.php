<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle cancellation
if(isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    $cancel_sql = "UPDATE bookings SET status='cancelled' WHERE id='$cancel_id' AND user_id='$user_id'";
    mysqli_query($conn, $cancel_sql);
    header("Location: my_booking.php?msg=cancelled");
    exit();
}

// Get bookings - Simple query
$sql = "SELECT b.*, h.rooms, h.capacity, h.description 
        FROM bookings b
        LEFT JOIN hotels h ON b.hotel_name = h.hotel_name 
        WHERE b.user_id='$user_id' 
        ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include "../header.php"; ?>
    
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .booking-card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .badge { padding: 5px 10px; border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h3 class="mb-4">My Bookings</h3>
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Booking cancelled successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($booking = mysqli_fetch_assoc($result)): ?>
        <div class="booking-card">
            <div class="row">
                <div class="col-md-8">
                    <h5><?php echo $booking['hotel_name']; ?></h5>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $booking['location']; ?>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-calendar"></i> <?php echo $booking['booking_date']; ?>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-bed"></i> <?php echo $booking['rooms'] ?? '1'; ?> Rooms
                        | <i class="fas fa-users"></i> Max <?php echo $booking['capacity'] ?? '2'; ?> Guests
                    </p>
                    <h5 class="text-success mt-2">৳ <?php echo $booking['price']; ?></h5>
                </div>
                
                <div class="col-md-4 text-right">
                    <?php 
                    $badge_class = 'badge-warning';
                    if($booking['status'] == 'confirmed') $badge_class = 'badge-success';
                    if($booking['status'] == 'cancelled') $badge_class = 'badge-danger';
                    ?>
                    <span class="badge <?php echo $badge_class; ?> mb-2">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                    
                    <br>
                    <small class="text-muted">ID: #<?php echo $booking['id']; ?></small>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-info btn-sm mb-1" onclick="showDetails(<?php echo $booking['id']; ?>)">
                            <i class="fas fa-eye"></i> Details
                        </button>
                        
                        <?php if($booking['status'] != 'cancelled'): ?>
                            <a href="?cancel_id=<?php echo $booking['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Cancel this booking?')">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">📅</div>
            <h4>No bookings found</h4>
            <p class="text-muted mb-4">You haven't made any bookings yet.</p>
            <a href="../hotel/hotel_list.php" class="btn btn-primary">
                <i class="fas fa-hotel"></i> Browse Hotels
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function showDetails(bookingId) {
    alert('Booking ID: ' + bookingId + '\nView details functionality would be implemented here.');
}
</script>

</body>
</html>