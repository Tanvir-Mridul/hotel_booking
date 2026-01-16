<?php
session_start();
include "../db_connect.php";
include "../includes/notification_helper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get hotel ID
$hotel_sql = "SELECT id, hotel_name FROM hotels WHERE owner_id='$owner_id' LIMIT 1";
$hotel_result = mysqli_query($conn, $hotel_sql);
if (mysqli_num_rows($hotel_result) == 0) {
    die("You need to create a hotel first");
}
$hotel = mysqli_fetch_assoc($hotel_result);
$hotel_id = $hotel['id'];

// Update booking status
if (isset($_GET['update_booking_status'])) {
    $booking_id = intval($_GET['booking_id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    
    // Verify ownership through room
    $verify_sql = "SELECT b.* FROM bookings b
                   LEFT JOIN rooms r ON b.room_id = r.id
                   WHERE b.id='$booking_id' 
                   AND (b.hotel_id='$hotel_id' OR r.hotel_id='$hotel_id')";
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (mysqli_num_rows($verify_result) > 0) {
        $booking = mysqli_fetch_assoc($verify_result);
        
        // Update booking status
        mysqli_query($conn, "UPDATE bookings SET status='$status' WHERE id='$booking_id'");
        
        // Update room active status if cancelled
        if ($status == 'cancelled' && !empty($booking['room_id'])) {
            // Check what column exists in rooms table
            $check_col_sql = "SHOW COLUMNS FROM rooms LIKE 'active'";
            $col_result = mysqli_query($conn, $check_col_sql);
            
            if (mysqli_num_rows($col_result) > 0) {
                // If 'active' column exists
                mysqli_query($conn, "UPDATE rooms SET active='1' WHERE id='{$booking['room_id']}'");
            } else {
                // Check for 'availability' column
                $check_avail_sql = "SHOW COLUMNS FROM rooms LIKE 'availability'";
                $avail_result = mysqli_query($conn, $check_avail_sql);
                if (mysqli_num_rows($avail_result) > 0) {
                    mysqli_query($conn, "UPDATE rooms SET availability='available' WHERE id='{$booking['room_id']}'");
                }
            }
        }
        
        // Send notification to user
        $item_name = !empty($booking['room_title']) ? $booking['room_title'] : $booking['hotel_name'];
        $user_message = "Booking status updated for '$item_name' to: " . ucfirst($status);
        
        sendNotification($booking['user_id'], 'user', $user_message, "/hotel_booking/user/my_booking.php");
        
        header("Location: manage_bookings.php?msg=updated");
        exit();
    }
}

// Get all bookings for this hotel
$bookings_sql = "SELECT 
    b.*, 
    u.name as user_name,
    u.email as user_email,
    r.room_title,
    r.capacity,
    r.active as room_active
FROM bookings b
JOIN users u ON b.user_id = u.id
LEFT JOIN rooms r ON b.room_id = r.id
WHERE b.hotel_id = '$hotel_id' 
   OR (b.room_id IS NOT NULL AND r.hotel_id = '$hotel_id')
ORDER BY 
    CASE 
        WHEN b.status = 'pending' THEN 1
        WHEN b.status = 'confirmed' THEN 2
        WHEN b.status = 'cancelled' THEN 3
        ELSE 4
    END,
    b.id DESC";

$bookings_result = mysqli_query($conn, $bookings_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .main-content { padding: 20px; margin-top: 70px; }
        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .badge-pending { background: #f39c12; color: white; }
        .badge-confirmed { background: #2ecc71; color: white; }
        .badge-cancelled { background: #e74c3c; color: white; }
        .room-status-badge {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
        }
        .room-active { background: #d4edda; color: #155724; }
        .room-inactive { background: #f8d7da; color: #721c24; }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }
        .filter-buttons .btn { margin-right: 5px; margin-bottom: 5px; }
        .contact-info { font-size: 13px; }
        .action-buttons { min-width: 120px; }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt"></i> Manage Bookings</h2>
            <div>
                <span class="badge badge-light">Hotel: <?php echo $hotel['hotel_name']; ?></span>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                $msg = $_GET['msg'];
                if($msg == 'updated') echo "Booking status updated successfully!";
                ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        
        <!-- Filter Buttons -->
        <div class="filter-buttons mb-4">
            <a href="?filter=all" class="btn btn-outline-primary btn-sm">All</a>
            <a href="?filter=pending" class="btn btn-outline-warning btn-sm">Pending</a>
            <a href="?filter=confirmed" class="btn btn-outline-success btn-sm">Confirmed</a>
            <a href="?filter=cancelled" class="btn btn-outline-danger btn-sm">Cancelled</a>
        </div>
        
        <!-- Bookings List -->
        <div class="row">
            <?php if(mysqli_num_rows($bookings_result) > 0): 
                $has_bookings = false;
                while($booking = mysqli_fetch_assoc($bookings_result)): 
                    // Apply filter
                    $filter = $_GET['filter'] ?? 'all';
                    if ($filter != 'all' && $booking['status'] != $filter) {
                        continue;
                    }
                    $has_bookings = true;
                    
                    // Determine badge class
                    $badge_class = 'badge-' . $booking['status'];
                    
                    // Room status
                    $room_status_badge = '';
                    if(isset($booking['room_active'])) {
                        $room_status_badge = $booking['room_active'] == 1 
                            ? '<span class="room-status-badge room-active">Available</span>' 
                            : '<span class="room-status-badge room-inactive">Booked</span>';
                    }
            ?>
                    <div class="col-md-6">
                        <div class="booking-card">
                            <div class="row">
                                <!-- User Info -->
                                <div class="col-md-3 text-center">
                                    <div class="user-avatar mx-auto mb-2">
                                        <?php echo strtoupper(substr($booking['user_name'], 0, 1)); ?>
                                    </div>
                                    <h6 class="mb-1"><?php echo $booking['user_name']; ?></h6>
                                    <small class="text-muted">
                                        <?php 
                                        // Check if booking_date is not null
                                        if (!empty($booking['booking_date'])) {
                                            echo date('d M', strtotime($booking['booking_date']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </small>
                                </div>
                                
                                <!-- Booking Details -->
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        <?php if(!empty($booking['room_title'])): ?>
                                            <i class="fas fa-bed text-primary"></i> 
                                            <?php echo $booking['room_title']; ?>
                                            <?php echo $room_status_badge; ?>
                                        <?php else: ?>
                                            <i class="fas fa-hotel text-primary"></i> 
                                            <?php echo $hotel['hotel_name']; ?>
                                        <?php endif; ?>
                                    </h6>
                                    
                                    <!-- Date Section -->
                                    <?php 
                                    $check_in = $booking['check_in_date'] ?? '';
                                    $check_out = $booking['check_out_date'] ?? '';
                                    
                                    if (!empty($check_in) && !empty($check_out) && $check_in != '0000-00-00' && $check_out != '0000-00-00'):
                                    ?>
                                        <p class="mb-1">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo date('d M', strtotime($check_in)); ?> - 
                                            <?php echo date('d M Y', strtotime($check_out)); ?>
                                        </p>
                                        
                                        <?php 
                                        // Calculate nights
                                        $nights = 1;
                                        if ($check_in && $check_out && $check_in != '0000-00-00' && $check_out != '0000-00-00') {
                                            $time1 = strtotime($check_in);
                                            $time2 = strtotime($check_out);
                                            if ($time1 && $time2 && $time2 > $time1) {
                                                $nights = ($time2 - $time1) / (60 * 60 * 24);
                                                $nights = max(1, $nights);
                                            }
                                        }
                                        ?>
                                        <small class="text-muted"><?php echo $nights; ?> night(s)</small>
                                    <?php elseif(!empty($booking['booking_date'])): ?>
                                        <p class="mb-1">
                                            <i class="fas fa-calendar"></i>
                                            Booked on: <?php echo date('d M Y', strtotime($booking['booking_date'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Guests and Rooms -->
                                    <?php if(!empty($booking['guests'])): ?>
                                        <p class="mb-1">
                                            <i class="fas fa-users"></i>
                                            <?php echo $booking['guests']; ?> Guest(s)
                                            <?php if(!empty($booking['rooms_count']) && $booking['rooms_count'] > 1): ?>
                                                | <?php echo $booking['rooms_count']; ?> Room(s)
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Contact Info -->
                                    <div class="contact-info">
                                        <?php if(!empty($booking['user_email'])): ?>
                                            <p class="mb-0">
                                                <i class="fas fa-envelope"></i>
                                                <?php echo $booking['user_email']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Status & Actions -->
                                <div class="col-md-3">
                                    <div class="text-right mb-2">
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <h5 class="text-success text-right">
                                        ৳ <?php echo number_format($booking['price'], 2); ?>
                                    </h5>
                                    
                                    <!-- Action Buttons -->
                                    <div class="btn-group-vertical w-100">
                                        <?php if($booking['status'] == 'pending'): ?>
                                            <a href="?update_booking_status=1&booking_id=<?php echo $booking['id']; ?>&status=confirmed"
                                               class="btn btn-sm btn-success mb-1"
                                               onclick="return confirm('Confirm this booking?')">
                                               <i class="fas fa-check"></i> Confirm
                                            </a>
                                            <a href="?update_booking_status=1&booking_id=<?php echo $booking['id']; ?>&status=cancelled"
                                               class="btn btn-sm btn-danger mb-1"
                                               onclick="return confirm('Reject this booking?')">
                                               <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php elseif($booking['status'] == 'confirmed'): ?>
                                            <a href="?update_booking_status=1&booking_id=<?php echo $booking['id']; ?>&status=cancelled"
                                               class="btn btn-sm btn-danger mb-1"
                                               onclick="return confirm('Cancel this booking?')">
                                               <i class="fas fa-times"></i> Cancel
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Chat Button -->
                                        <a href="../chat/chat.php?user_id=<?php echo $booking['user_id']; ?>" 
                                           class="btn btn-sm btn-info mb-1">
                                           <i class="fas fa-comment"></i> Chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Booking ID -->
                            <div class="mt-3 pt-3 border-top text-center">
                                <small class="text-muted">
                                    Booking ID: #<?php echo $booking['id']; ?>
                                    <?php if(!empty($booking['room_id'])): ?>
                                        | Room ID: #<?php echo $booking['room_id']; ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; 
                
                if (!$has_bookings && isset($_GET['filter'])): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No <?php echo ucfirst($_GET['filter']); ?> Bookings</h4>
                            <p class="text-muted">No bookings found with this status.</p>
                            <a href="?filter=all" class="btn btn-primary">View All Bookings</a>
                        </div>
                    </div>
                <?php endif;
                
                else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h4>No Bookings Yet</h4>
                        <p class="text-muted mb-4">You haven't received any bookings yet.</p>
                        <a href="upload_room.php" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Add Rooms
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Stats -->
        <?php 
        $stats_sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(price) as total_revenue
            FROM bookings 
            WHERE hotel_id = '$hotel_id'";
        
        $stats_result = mysqli_query($conn, $stats_sql);
        $stats = mysqli_fetch_assoc($stats_result);
        ?>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Booking Statistics</h5>
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
                        <h3 class="text-primary">৳ <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>
</body>
</html>