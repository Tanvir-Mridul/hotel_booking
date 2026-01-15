//edit_room.php//
<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get room and verify ownership
$room_sql = "SELECT r.*, h.hotel_name 
             FROM rooms r 
             JOIN hotels h ON r.hotel_id = h.id 
             WHERE r.id='$room_id' AND h.owner_id='$owner_id'";
$room_result = mysqli_query($conn, $room_sql);

if (mysqli_num_rows($room_result) == 0) {
    die("Room not found or access denied");
}

$room = mysqli_fetch_assoc($room_result);

// Update room
if (isset($_POST['update_room'])) {
    $room_title = mysqli_real_escape_string($conn, $_POST['room_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $capacity = intval($_POST['capacity']);
    $price_per_night = floatval($_POST['price_per_night']);
    $room_count = intval($_POST['room_count']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_sql = "UPDATE rooms SET 
        room_title='$room_title',
        description='$description',
        capacity='$capacity',
        price_per_night='$price_per_night',
        room_count='$room_count',
        status='$status'
        WHERE id='$room_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: manage_rooms.php?msg=updated");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}

include "../header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .main-content { padding: 20px; margin-top: 70px; max-width: 800px; margin-left: auto; margin-right: auto; }
        .form-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="main-content">
    <div class="form-container">
        <h3 class="mb-4">
            <i class="fas fa-edit"></i> Edit Room
            <small class="text-muted">| <?php echo $room['hotel_name']; ?></small>
        </h3>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Room Title *</label>
                        <input type="text" name="room_title" class="form-control" 
                               value="<?php echo $room['room_title']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo $room['description']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Capacity (Persons) *</label>
                        <input type="number" name="capacity" class="form-control" 
                               value="<?php echo $room['capacity']; ?>" min="1" max="10" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Price Per Night (৳) *</label>
                        <input type="number" name="price_per_night" class="form-control" 
                               value="<?php echo $room['price_per_night']; ?>" step="0.01" min="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Number of Rooms *</label>
                        <input type="number" name="room_count" class="form-control" 
                               value="<?php echo $room['room_count']; ?>" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="booked" <?php echo $room['status'] == 'booked' ? 'selected' : ''; ?>>Booked</option>
                            <option value="maintenance" <?php echo $room['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Image</label><br>
                        <?php if(!empty($room['image'])): ?>
                            <img src="../uploads/rooms/<?php echo $room['image']; ?>" 
                                 style="max-width: 200px; border-radius: 5px; margin-bottom: 10px;">
                            <br>
                        <?php else: ?>
                            <div class="text-muted">No image uploaded</div>
                        <?php endif; ?>
                        <input type="file" name="new_image" class="form-control-file" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" name="update_room" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Room
                </button>
                <a href="manage_rooms.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>