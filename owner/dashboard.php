<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get hotel info
$hotel_sql = "SELECT * FROM hotels WHERE owner_id='$owner_id' LIMIT 1";
$hotel_result = mysqli_query($conn, $hotel_sql);

if (mysqli_num_rows($hotel_result) == 0) {
    // Create default hotel if not exists
    $owner_name = $_SESSION['name'];
    $default_hotel_name = $owner_name . "'s Hotel";
    
    $create_hotel = "INSERT INTO hotels (owner_id, hotel_name, location, status) 
                     VALUES ('$owner_id', '$default_hotel_name', 'Dhaka', 'pending')";
    mysqli_query($conn, $create_hotel);
    
    $hotel_result = mysqli_query($conn, $hotel_sql);
}

$hotel = mysqli_fetch_assoc($hotel_result);
$hotel_id = $hotel['id'];

// Get stats - FIXED: removed status condition for rooms
$total_rooms = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM rooms WHERE hotel_id='$hotel_id'"
))['total'];

// FIXED: available rooms without status condition
$available_rooms = $total_rooms; // Default to total rooms if no status column

// Check if active column exists
$check_active = mysqli_query($conn, "SHOW COLUMNS FROM rooms LIKE 'active'");
if (mysqli_num_rows($check_active) > 0) {
    $available_rooms = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT COUNT(*) as available FROM rooms 
         WHERE hotel_id='$hotel_id' AND active = 1"
    ))['available'];
}

// FIXED: total bookings
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as bookings 
     FROM bookings 
     WHERE hotel_id='$hotel_id'"
))['bookings'];

// Premium check
$is_premium = false;
$remaining_days = 0;
$sub_q = mysqli_query($conn, "SELECT end_date, DATEDIFF(end_date, CURDATE()) AS remaining_days
    FROM owner_subscriptions
    WHERE owner_id='$owner_id' AND status='approved' 
    ORDER BY id DESC LIMIT 1");

if ($sub_q && mysqli_num_rows($sub_q) > 0) {
    $sub = mysqli_fetch_assoc($sub_q);
    $remaining_days = (int)$sub['remaining_days'];
    if ($remaining_days > 0) {
        $is_premium = true;
    }
}

include "../header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .main-content { padding: 20px; margin-top: 70px; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .hotel-info {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .hotel-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="main-content">
    <!-- Premium Banner -->
    <?php if($is_premium): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong>⭐ Premium Owner</strong> | <?= $remaining_days ?> days remaining
        </div>
        <a href="subscription.php" class="btn btn-sm btn-outline-warning">Extend</a>
    </div>
    <?php else: ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong>Free Plan</strong> - Upgrade for more features
        </div>
        <a href="subscription.php" class="btn btn-sm btn-warning">Upgrade Now</a>
    </div>
    <?php endif; ?>
    
    <!-- Hotel Info -->
    <div class="hotel-info">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <?php if(!empty($hotel['image'])): ?>
                    <img src="../uploads/<?php echo $hotel['image']; ?>" class="hotel-image">
                <?php else: ?>
                    <div class="hotel-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-hotel fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h3><?php echo $hotel['hotel_name']; ?></h3>
                <p class="text-muted mb-2">
                    <i class="fas fa-map-marker-alt"></i> <?php echo $hotel['location']; ?>
                </p>
                <?php if(!empty($hotel['description'])): ?>
                    <p><?php echo substr($hotel['description'], 0, 150); ?>...</p>
                <?php endif; ?>
                <div class="action-buttons">
                    <a href="hotel_settings.php" class="btn-action btn-primary">
                        <i class="fas fa-cog"></i> Hotel Settings
                    </a>
                    <a href="upload_room.php" class="btn-action btn-success">
                        <i class="fas fa-plus-circle"></i> Add Room
                    </a>
                    <a href="manage_rooms.php" class="btn-action btn-info">
                        <i class="fas fa-bed"></i> Manage Rooms
                    </a>
                    <a href="/hotel_booking/owner/manage_bookings.php" class="btn-action btn-warning">
                        <i class="fas fa-calendar-alt"></i> manage_bookings
                    </a>
                    <a href="/hotel_booking/owner/finance.php" class="btn-action btn-primary">
                        <i class="fas fa-calendar-alt"></i> Finsnce
                    </a>
                    <a href="manage_dates.php" class="btn-action btn-success">
                        <i class="fas fa-plus-circle"></i> Manage Date
                    </a>
                    <a href="reports.php" class="btn-action btn-success">
                        <i class="fas fa-plus-circle"></i> Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon text-primary">
                <i class="fas fa-bed"></i>
            </div>
            <div class="stat-number"><?php echo $total_rooms; ?></div>
            <div class="stat-label">Total Rooms</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo $available_rooms; ?></div>
            <div class="stat-label">Available Rooms</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon text-warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number"><?php echo $total_bookings; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon text-info">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-number">
                <?php echo $is_premium ? 'Premium' : 'Free'; ?>
            </div>
            <div class="stat-label">Plan</div>
        </div>
    </div>
    
    <!-- Recent Rooms -->
    <div class="hotel-info">
        <h5 class="mb-3">Recent Rooms</h5>
        <?php
        $rooms_sql = "SELECT * FROM rooms WHERE hotel_id='$hotel_id' ORDER BY id DESC LIMIT 5";
        $rooms_result = mysqli_query($conn, $rooms_sql);
        
        if(mysqli_num_rows($rooms_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($room = mysqli_fetch_assoc($rooms_result)): 
                            // FIXED: Determine status
                            $status = 'available';
                            if (isset($room['active']) && $room['active'] == 0) {
                                $status = 'inactive';
                            }
                            
                            $badge_class = 'badge-success';
                            if ($status == 'inactive') $badge_class = 'badge-secondary';
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo $room['room_title']; ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo substr($room['description'], 0, 50); ?>...
                                </small>
                            </td>
                            <td><?php echo $room['capacity']; ?> Persons</td>
                            <td class="text-success">৳ <?php echo $room['price_per_night']; ?></td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_room.php?id=<?php echo $room['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <a href="manage_rooms.php" class="btn btn-outline-primary btn-sm">
                View All Rooms
            </a>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                <h5>No Rooms Added Yet</h5>
                <p class="text-muted">Start by adding your first room</p>
                <a href="upload_room.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add First Room
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>