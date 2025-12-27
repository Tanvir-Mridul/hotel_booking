<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include "../db_connect.php";
include "../header.php";

$user_id = $_SESSION['user_id'];

// Get user's booking stats
$bookings_sql = "SELECT COUNT(*) as total_bookings, 
                        SUM(price) as total_spent 
                 FROM bookings 
                 WHERE user_id='$user_id'";
$bookings_result = mysqli_query($conn, $bookings_sql);
$bookings_stats = mysqli_fetch_assoc($bookings_result);

$total_bookings = $bookings_stats['total_bookings'] ?? 0;
$total_spent = $bookings_stats['total_spent'] ?? 0;

// Get recent bookings
$recent_sql = "SELECT * FROM bookings 
               WHERE user_id='$user_id' 
               ORDER BY booking_date DESC 
               LIMIT 10";
$recent_result = mysqli_query($conn, $recent_sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 15px;
        }
        
        /* Bookings Table */
        .bookings-table {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        
        .table-title {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        
        .empty-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Page Title -->
    <h3 class="mb-4" style="color: #2c3e50;">User Dashboard</h3>
    
    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-number"><?php echo $total_bookings; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number">৳ <?php echo number_format($total_spent); ?></div>
            <div class="stat-label">Total Amount</div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number">0</div>
            <div class="stat-label">Notifications</div>
        </div>
    </div>
    
    <!-- Recent Bookings -->
    <div class="bookings-table">
        <h4 class="table-title">Recent Bookings</h4>
        
        <?php if(mysqli_num_rows($recent_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hotel Name</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        while($booking = mysqli_fetch_assoc($recent_result)): 
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $booking['hotel_name']; ?></td>
                            <td><?php echo $booking['location']; ?></td>
                            <td><?php echo $booking['booking_date']; ?></td>
                            <td>৳ <?php echo $booking['price']; ?></td>
                            <td>
                                <?php 
                                $status_class = 'status-pending';
                                if($booking['status'] == 'confirmed') $status_class = 'status-confirmed';
                                if($booking['status'] == 'cancelled') $status_class = 'status-cancelled';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h4>No Bookings Yet</h4>
                <p class="text-muted">You haven't made any bookings yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>