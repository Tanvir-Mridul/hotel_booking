
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

// ===== AMENITIES LOAD 
$all_amenities = mysqli_query($conn, "SELECT * FROM amenities ORDER BY name ASC");

$selected_amenities = [];
$sa_q = mysqli_query($conn, "SELECT amenity_id FROM room_amenities WHERE room_id='$room_id'");
while ($row = mysqli_fetch_assoc($sa_q)) {
    $selected_amenities[] = $row['amenity_id'];
}


// Update room
if (isset($_POST['update_room'])) {

    $room_title = mysqli_real_escape_string($conn, $_POST['room_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $capacity = intval($_POST['capacity']);
    $price_per_night = floatval($_POST['price_per_night']);
    $discount_price = !empty($_POST['discount_price']) 
    ? floatval($_POST['discount_price']) 
    : NULL;

    $room_count = intval($_POST['room_count']);

    // IMAGE UPDATE PART
    $image_sql = "";
   // ===== IMAGE UPDATE (room_images table) 
if (!empty($_FILES['new_image']['name'])) {

    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {

        $new_image_name = time() . "_room_" . $room_id . "." . $ext;
        $upload_path = "../uploads/rooms/" . $new_image_name;

        if (!is_dir("../uploads/rooms")) {
            mkdir("../uploads/rooms", 0755, true);
        }

        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $upload_path)) {

            // purono primary image remove
            mysqli_query($conn, "DELETE FROM room_images WHERE room_id='$room_id'");

            // new image insert as primary
            mysqli_query($conn, "
                INSERT INTO room_images (room_id, image_url, is_primary)
                VALUES ('$room_id', '$new_image_name', 1)
            ");
        }
    }
}

   $update_sql = "UPDATE rooms SET
    room_title='$room_title',
    description='$description',
    capacity='$capacity',
    room_count='$room_count',
    price_per_night='$price_per_night',
    discount_price=".($discount_price !== NULL ? "'$discount_price'" : "NULL")."
WHERE id='$room_id'";


// ===== AMENITIES UPDATE =====
mysqli_query($conn, "DELETE FROM room_amenities WHERE room_id='$room_id'");

if (!empty($_POST['amenities'])) {
    foreach ($_POST['amenities'] as $amenity_id) {
        $amenity_id = intval($amenity_id);
        mysqli_query(
            $conn,
            "INSERT INTO room_amenities (room_id, amenity_id)
             VALUES ('$room_id', '$amenity_id')"
        );
    }
}

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
        
        <form method="POST" action="" enctype="multipart/form-data">

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
                
                <!-- ===== ROOM AMENITIES ===== -->
<div class="form-group mt-3">
    <label><strong>Room Amenities</strong></label>
    <div class="row">
        <?php while($am = mysqli_fetch_assoc($all_amenities)): ?>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="amenities[]"
                           value="<?php echo $am['id']; ?>"
                           id="amenity_<?php echo $am['id']; ?>"
                           <?php echo in_array($am['id'], $selected_amenities) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="amenity_<?php echo $am['id']; ?>">
                        <?php echo $am['name']; ?>
                    </label>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

                <div class="col-md-6">
                    <div class="form-group">
    <label>Main Price (৳)</label>
    <input type="number" name="price_per_night" class="form-control"
           value="<?php echo $room['price_per_night']; ?>" required>
</div>

<div class="form-group">
    <label>Discount Price (৳)</label>
    <input type="number" name="discount_price" class="form-control"
           value="<?php echo $room['discount_price']; ?>">
</div>

                    
                    <div class="form-group">
                        <label>Number of Rooms *</label>
                        <input type="number" name="room_count" class="form-control" 
                               value="<?php echo $room['room_count']; ?>" min="1" required>
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