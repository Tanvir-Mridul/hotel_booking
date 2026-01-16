<?php
session_start();
include "../db_connect.php";

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

// Add new room
if (isset($_POST['add_room'])) {
    $room_title = mysqli_real_escape_string($conn, $_POST['room_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $capacity = intval($_POST['capacity']);
    $price_per_night = floatval($_POST['price_per_night']);
    $room_count = intval($_POST['room_count'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Check if using 'active' or 'is_active' column
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM rooms LIKE 'active'");
    
    if (mysqli_num_rows($col_check) > 0) {
        // If 'active' column exists
        $insert_sql = "INSERT INTO rooms 
            (hotel_id, room_title, description, capacity, price_per_night, room_count, active) 
            VALUES 
            ('$hotel_id', '$room_title', '$description', '$capacity', '$price_per_night', '$room_count', '$is_active')";
    } else {
        // If 'is_active' column exists
        $insert_sql = "INSERT INTO rooms 
            (hotel_id, room_title, description, capacity, price_per_night, room_count, is_active) 
            VALUES 
            ('$hotel_id', '$room_title', '$description', '$capacity', '$price_per_night', '$room_count', '$is_active')";
    }
    
    if (mysqli_query($conn, $insert_sql)) {
        header("Location: manage_rooms.php?msg=added");
        exit();
    } else {
        $error = "Failed to add room: " . mysqli_error($conn);
    }
}

// Toggle room active status
if (isset($_GET['toggle_active'])) {
    $room_id = intval($_GET['room_id']);
    
    // Check which column exists
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM rooms LIKE 'active'");
    
    if (mysqli_num_rows($col_check) > 0) {
        // Use 'active' column
        $verify_sql = "SELECT id, active FROM rooms WHERE id='$room_id' AND hotel_id='$hotel_id'";
        $verify_result = mysqli_query($conn, $verify_sql);
        
        if (mysqli_num_rows($verify_result) > 0) {
            $room_data = mysqli_fetch_assoc($verify_result);
            $new_status = $room_data['active'] ? 0 : 1;
            
            mysqli_query($conn, "UPDATE rooms SET active='$new_status' WHERE id='$room_id'");
            header("Location: manage_rooms.php?msg=updated");
            exit();
        }
    } else {
        // Use 'is_active' column
        $verify_sql = "SELECT id, is_active FROM rooms WHERE id='$room_id' AND hotel_id='$hotel_id'";
        $verify_result = mysqli_query($conn, $verify_sql);
        
        if (mysqli_num_rows($verify_result) > 0) {
            $room_data = mysqli_fetch_assoc($verify_result);
            $new_status = $room_data['is_active'] ? 0 : 1;
            
            mysqli_query($conn, "UPDATE rooms SET is_active='$new_status' WHERE id='$room_id'");
            header("Location: manage_rooms.php?msg=updated");
            exit();
        }
    }
}

// Delete room
if (isset($_GET['delete_room'])) {
    $room_id = intval($_GET['delete_room']);
    
    $verify_sql = "SELECT id FROM rooms WHERE id='$room_id' AND hotel_id='$hotel_id'";
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Check if room has active bookings
        $booking_check = mysqli_query($conn, 
            "SELECT id FROM bookings WHERE room_id='$room_id' AND status IN ('pending', 'confirmed')");
        
        if (mysqli_num_rows($booking_check) > 0) {
            header("Location: manage_rooms.php?msg=error&error=Cannot delete room with active bookings");
            exit();
        }
        
        mysqli_query($conn, "DELETE FROM room_images WHERE room_id='$room_id'");
        mysqli_query($conn, "DELETE FROM rooms WHERE id='$room_id'");
        header("Location: manage_rooms.php?msg=deleted");
        exit();
    }
}

// Get all rooms with their primary image - FIXED QUERY
$rooms_sql = "SELECT r.*, 
              (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as primary_image
              FROM rooms r 
              WHERE r.hotel_id='$hotel_id' 
              ORDER BY r.id DESC";
$rooms_result = mysqli_query($conn, $rooms_sql);

include "../header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .main-content { padding: 20px; margin-top: 70px; }
        .room-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            border-left: 5px solid #28a745;
        }
        .room-card.inactive { 
            border-left-color: #dc3545; 
            opacity: 0.7; 
        }
        
        .room-image-container {
            height: 180px;
            background: #f8f9fa;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .room-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .room-details { padding: 15px; }
        .room-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .room-description {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
            max-height: 40px;
            overflow: hidden;
        }
        .room-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .room-price {
            color: #28a745;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .btn-action {
            padding: 5px 10px;
            font-size: 12px;
            margin: 2px;
        }
        .add-room-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-bed"></i> Manage Rooms</h2>
            <div>
                <span class="badge badge-light">Hotel: <?php echo $hotel['hotel_name']; ?></span>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert 
                <?php echo ($_GET['msg'] == 'error') ? 'alert-danger' : 'alert-success'; ?> 
                alert-dismissible fade show">
                <?php
                $msg = $_GET['msg'];
                if($msg == 'added') echo "Room added successfully!";
                elseif($msg == 'updated') echo "Room status updated successfully!";
                elseif($msg == 'deleted') echo "Room deleted successfully!";
                elseif($msg == 'warning') echo htmlspecialchars($_GET['error'] ?? '');
                elseif($msg == 'error') echo htmlspecialchars($_GET['error'] ?? '');
                ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        
       
        <!-- Rooms List -->
        <h4 class="mt-5 mb-3">Your Rooms</h4>
        <div class="row">
            <?php if(mysqli_num_rows($rooms_result) > 0): ?>
                <?php while($room = mysqli_fetch_assoc($rooms_result)): 
                    // Determine active status based on column name
                    $is_active = 0;
                    if (isset($room['active'])) {
                        $is_active = $room['active'];
                    } elseif (isset($room['is_active'])) {
                        $is_active = $room['is_active'];
                    }
                    
                    $card_class = $is_active ? 'room-card' : 'room-card inactive';
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="<?php echo $card_class; ?>">
                            <!-- Room Image -->
                            <div class="room-image-container">
                                <?php if(!empty($room['primary_image'])): ?>
                                    <img src="../uploads/rooms/<?php echo $room['primary_image']; ?>" 
                                         class="room-image" alt="<?php echo $room['room_title']; ?>"
                                         onerror="this.onerror=null; this.src='../assets/img/default-room.jpg'; this.style.padding='40px';">
                                <?php else: ?>
                                    <i class="fas fa-bed fa-4x text-muted"></i>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Room Details -->
                            <div class="room-details">
                                <div class="room-title">
                                    <?php echo htmlspecialchars($room['room_title']); ?>
                                    <span class="badge <?php echo $is_active ? 'badge-success' : 'badge-danger'; ?> float-right">
                                        <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                
                                <?php if(!empty($room['description'])): ?>
                                    <div class="room-description">
                                        <?php echo htmlspecialchars(substr($room['description'], 0, 80)); ?>
                                        <?php if(strlen($room['description']) > 80): ?>...<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="room-info">
                                    <span>
                                        <i class="fas fa-users text-info"></i>
                                        <?php echo $room['capacity']; ?> Persons
                                    </span>
                                    <span>
                                        <i class="fas fa-door-closed text-success"></i>
                                        <?php echo $room['room_count']; ?> Rooms
                                    </span>
                                </div>
                                
                                <div class="room-price">
                                    ৳ <?php echo number_format($room['price_per_night'], 2); ?> / night
                                </div>
                                
                                <!-- Action Bar -->
                                <div class="action-bar">
                                    <!-- Active/Inactive Toggle -->
                                    <a href="?toggle_active=1&room_id=<?php echo $room['id']; ?>" 
                                       class="btn btn-sm <?php echo $is_active ? 'btn-warning' : 'btn-success'; ?>"
                                       onclick="return confirm('<?php echo $is_active ? "Deactivate" : "Activate"; ?> this room?')">
                                        <?php echo $is_active ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    
                                    <!-- Action Buttons -->
                                    <div>
                                        <a href="edit_room.php?id=<?php echo $room['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary btn-action">
                                           <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete_room=<?php echo $room['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger btn-action"
                                           onclick="return confirm('Delete this room? This action cannot be undone.')">
                                           <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-bed fa-4x text-muted mb-4"></i>
                        <h4>No Rooms Added Yet</h4>
                        <p class="text-muted mb-4">Add your first room using the form above</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Important: Filter rooms on your website -->
        <div class="alert alert-info mt-4">
            <h5><i class="fas fa-info-circle"></i> Important Note</h5>
            <p class="mb-0">
                When users search for rooms on your website, make sure your website code filters by active status.
                Add this condition to your room search query: <code>WHERE active = 1</code>
            </p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script>
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>
</body>
</html>