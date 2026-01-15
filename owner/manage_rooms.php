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
    
    $insert_sql = "INSERT INTO rooms 
        (hotel_id, room_title, description, capacity, price_per_night, room_count, is_active) 
        VALUES 
        ('$hotel_id', '$room_title', '$description', '$capacity', '$price_per_night', '$room_count', '$is_active')";
    
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

// Get all rooms with their primary image
$rooms_sql = "SELECT r.*, 
              (SELECT image_url FROM room_images WHERE room_id = r.id AND is_primary = 1 LIMIT 1) as primary_image
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
        .room-card.inactive { border-left-color: #dc3545; opacity: 0.7; }
        
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
        <div class="row">
            <?php if(mysqli_num_rows($rooms_result) > 0): ?>
                <?php while($room = mysqli_fetch_assoc($rooms_result)): 
                    $is_active = $room['is_active'];
                    $card_class = $is_active ? 'room-card' : 'room-card inactive';
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="<?php echo $card_class; ?>">
                            <!-- Room Image -->
                            <div class="room-image-container">
                                <?php if(!empty($room['primary_image'])): ?>
                                    <img src="../uploads/rooms/<?php echo $room['primary_image']; ?>" 
                                         class="room-image" alt="<?php echo $room['room_title']; ?>"
                                         onerror="this.src='../assets/img/default.jpg'">
                                <?php else: ?>
                                    <i class="fas fa-bed fa-4x text-muted"></i>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Room Details -->
                            <div class="room-details">
                                <div class="room-title">
                                    <?php echo $room['room_title']; ?>
                                    <span class="badge <?php echo $is_active ? 'badge-success' : 'badge-danger'; ?> float-right">
                                        <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                
                                <?php if(!empty($room['description'])): ?>
                                    <div class="room-description">
                                        <?php echo substr($room['description'], 0, 80); ?>
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
                                       class="btn btn-sm <?php echo $is_active ? 'btn-warning' : 'btn-success'; ?>">
                                        <?php echo $is_active ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    
                                    <!-- Action Buttons -->
                                    <div>
                                        <a href="edit_room.php?id=<?php echo $room['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary btn-action">
                                           <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete_room=<?php echo $room['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger btn-action"
                                           onclick="return confirm('Delete this room?')">
                                           <i class="fas fa-trash"></i>
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
        
        <!-- Simple Add Room Form -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-plus-circle"></i> Add New Room</h5>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Room Title *</label>
                                <input type="text" name="room_title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Price per Night (৳) *</label>
                                <input type="number" name="price_per_night" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Capacity *</label>
                                <input type="number" name="capacity" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Number of Rooms *</label>
                                <input type="number" name="room_count" class="form-control" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" name="add_room" class="btn btn-primary">
                                <i class="fas fa-save"></i> Add Room
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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