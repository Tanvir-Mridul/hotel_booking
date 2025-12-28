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

    mysqli_query(
        $conn,
        "UPDATE bookings 
         SET status='cancelled' 
         WHERE id=$cancel_id AND user_id=$user_id"
    );

    header("Location: my_booking.php?msg=cancelled");
    exit();
}

/* ✅ Booking List (PROPER JOIN) */
$sql = "SELECT 
    b.*, 
    h.location, 
    h.rooms, 
    h.capacity 
FROM bookings b
LEFT JOIN hotels h ON b.hotel_id = h.id
WHERE b.user_id = $user_id
ORDER BY b.id DESC
";

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
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>

    <style>
        body { background: #f5f5f5; padding: 20px; }
        .booking-card { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .badge { padding: 6px 12px; border-radius: 20px; }
    </style>
</head>

<body>

<?php include "../header.php"; ?>

<div class="container">
    <h3 class="mb-4">My Bookings</h3>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'cancelled'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Booking cancelled successfully!
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($booking = mysqli_fetch_assoc($result)): ?>

            <div class="booking-card">
                <div class="row">

                    <div class="col-md-8">
                        <h5><?php echo htmlspecialchars($booking['hotel_name']); ?></h5>

                        <p class="text-muted mb-1">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($booking['location']); ?>
                        </p>

                        <p class="mb-1">
                            <i class="fas fa-calendar"></i>
                            <?php echo date("d M Y", strtotime($booking['booking_date'])); ?>
                        </p>

                        <p class="mb-1">
                            <i class="fas fa-bed"></i>
                            <?php echo $booking['rooms'] ?? 1; ?> Rooms |
                            <i class="fas fa-users"></i>
                            Max <?php echo $booking['capacity'] ?? 2; ?> Guests
                        </p>

                        <h5 class="text-success mt-2">
                            ৳ <?php echo number_format($booking['price']); ?>
                        </h5>
                    </div>

                    <div class="col-md-4 text-right">
                        <?php
                        $badge = "badge-warning";
                        if ($booking['status'] === 'confirmed') $badge = "badge-success";
                        if ($booking['status'] === 'cancelled') $badge = "badge-danger";
                        ?>

                        <span class="badge <?php echo $badge; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>

                        <br>
                        <small class="text-muted">Booking ID: #<?php echo $booking['id']; ?></small>

                        <div class="mt-3">
                            <?php if ($booking['status'] !== 'cancelled'): ?>
                                <a href="?cancel_id=<?php echo $booking['id']; ?>"
                                   onclick="return confirm('Cancel this booking?')"
                                   class="btn btn-danger btn-sm">
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
            <h4>No bookings found</h4>
            <a href="../hotel/hotel_list.php" class="btn btn-primary mt-3">
                Browse Hotels
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
