<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all bookings with user info
$sql = "SELECT b.*, 
       u.name AS user_name, 
       o.name AS owner_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN users o ON b.owner_id = o.id
ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; width: 100%; }
        .table-box { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge-confirmed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-pending { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3>Manage Bookings</h3>
    
    <div class="table-box">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Hotel</th>
                <th>Location</th>
                <th>Price</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <div><?php echo $row['user_name']; ?></div>
                        <small><?php echo $row['email']; ?></small>
                    </td>
                    <td><?php echo $row['hotel_name']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>৳ <?php echo $row['price']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <?php 
                        $badge_class = 'badge-pending';
                        if($row['status'] == 'confirmed') $badge_class = 'badge-confirmed';
                        if($row['status'] == 'cancelled') $badge_class = 'badge-cancelled';
                        ?>
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="delete_booking.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this booking?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No bookings found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>