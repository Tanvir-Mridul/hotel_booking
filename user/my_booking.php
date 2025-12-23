<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's bookings
$sql = "SELECT * FROM bookings WHERE user_id='$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; width: 100%; }
        
        .booking-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #3498db;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .confirmed { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }
        .cancelled { background: #f8d7da; color: #721c24; }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .empty-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3>My Bookings</h3>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="booking-card">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h5 style="margin-bottom: 5px;"><?php echo $row['hotel_name']; ?></h5>
                    <p style="color: #666; margin-bottom: 5px;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $row['location']; ?>
                    </p>
                    <p style="color: #666; margin-bottom: 5px;">
                        <i class="fas fa-calendar"></i> <?php echo $row['booking_date']; ?>
                    </p>
                    <p style="font-size: 18px; font-weight: bold; color: #27ae60;">
                        ৳ <?php echo $row['price']; ?>
                    </p>
                </div>
                
                <div style="text-align: right;">
                    <span class="status-badge <?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                    <br>
                    <small style="color: #999; margin-top: 5px; display: block;">
                        Booking ID: #<?php echo $row['id']; ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h4>No Bookings Yet</h4>
            <p style="color: #666; max-width: 400px; margin: 10px auto 20px;">
                You haven't made any bookings yet. Start exploring hotels and book your stay.
            </p>
            <a href="../hotel/hotel_list.php" class="btn btn-primary">
                <i class="fas fa-hotel"></i> Browse Hotels
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>