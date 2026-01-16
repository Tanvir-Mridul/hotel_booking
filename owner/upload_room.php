<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get hotel info
$hotel_sql = "SELECT id FROM hotels WHERE owner_id='$owner_id'";
$hotel_result = mysqli_query($conn, $hotel_sql);

if (mysqli_num_rows($hotel_result) == 0) {
    die("No hotel found. Please contact admin.");
}

$hotel = mysqli_fetch_assoc($hotel_result);
$hotel_id = $hotel['id'];

// Check subscription limit
$is_premium = false;
$sub_q = mysqli_query($conn, "SELECT id FROM owner_subscriptions 
    WHERE owner_id='$owner_id' AND status='approved' 
    AND end_date >= CURDATE() LIMIT 1");
if ($sub_q && mysqli_num_rows($sub_q) > 0) {
    $is_premium = true;
}

// Count rooms
$room_count_sql = "SELECT COUNT(*) as total FROM rooms WHERE hotel_id='$hotel_id'";
$room_count_result = mysqli_query($conn, $room_count_sql);
$room_count = mysqli_fetch_assoc($room_count_result)['total'];

// Free plan limit: 1 rooms
if (!$is_premium && $room_count >= 1) {
    echo '
    <div class="container mt-5">
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Room Limit Reached</h4>
            <p>Free plan allows maximum 1 rooms. Please upgrade to premium for unlimited rooms.</p>
            <a href="subscription.php" class="btn btn-warning mt-2">
                <i class="fas fa-crown"></i> Upgrade to Premium
            </a>
            <a href="dashboard.php" class="btn btn-secondary mt-2 ml-2">Back to Dashboard</a>
        </div>
    </div>';
   
    exit();
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_title = mysqli_real_escape_string($conn, $_POST['room_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $capacity = intval($_POST['capacity']);
    $room_count_input = intval($_POST['room_count']);
    $price_per_night = floatval($_POST['price_per_night']);
    
    // Validate
    if (empty($room_title) || $price_per_night <= 0) {
        $error_msg = "Please fill all required fields correctly!";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Insert room - REMOVED 'status' column
            $room_sql = "INSERT INTO rooms (hotel_id, room_title, description, 
                          capacity, room_count, price_per_night, active) 
                         VALUES ('$hotel_id', '$room_title', '$description', 
                                 '$capacity', '$room_count_input', '$price_per_night', '1')";
            
            if (!mysqli_query($conn, $room_sql)) {
                throw new Exception("Room insert failed: " . mysqli_error($conn));
            }
            
            $room_id = mysqli_insert_id($conn);
            $uploaded_images = 0;
            $primary_set = false;
            
            // 2. Handle images
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($uploaded_images >= 3) break; // Max 3 images
                    
                    if ($_FILES['images']['error'][$key] == 0) {
                        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                        $max_size = 2 * 1024 * 1024; // 2MB
                        
                        if (in_array($_FILES['images']['type'][$key], $allowed_types) && 
                            $_FILES['images']['size'][$key] <= $max_size) {
                            
                            // Generate unique filename
                            $file_ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                            $image_name = time() . "_room_" . $room_id . "_" . $key . "." . $file_ext;
                            $upload_path = "../uploads/rooms/" . $image_name;
                            
                            // Create rooms directory if not exists
                            if (!is_dir("../uploads/rooms")) {
                                mkdir("../uploads/rooms", 0755, true);
                            }
                            
                            if (move_uploaded_file($tmp_name, $upload_path)) {
                                // First image is primary
                                $is_primary = (!$primary_set) ? 1 : 0;
                                $primary_set = true;
                                
                                $image_sql = "INSERT INTO room_images (room_id, image_url, is_primary) 
                                              VALUES ('$room_id', '$image_name', '$is_primary')";
                                
                                if (!mysqli_query($conn, $image_sql)) {
                                    throw new Exception("Image insert failed: " . mysqli_error($conn));
                                }
                                
                                $uploaded_images++;
                            }
                        }
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Send notification to admin
            include "../includes/notification_helper.php";
            $admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
            if ($admin_q && mysqli_num_rows($admin_q) > 0) {
                $admin = mysqli_fetch_assoc($admin_q);
                $owner_name = $_SESSION['name'];
                
                sendNotification($admin['id'], 'admin',
                    "🏨 New room uploaded by $owner_name - \"$room_title\" (৳$price_per_night/night)",
                    "/hotel_booking/admin/hotels.php"
                );
            }
            
            $success_msg = "Room uploaded successfully! It will be reviewed by admin.";
            
            // Clear form
            $_POST = array();
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Room</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .upload-container { max-width: 800px; margin: auto; }
        .upload-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .image-preview-container { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .image-preview { width: 100px; height: 80px; object-fit: cover; border-radius: 5px; border: 2px solid #ddd; }
        .remove-image { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; cursor: pointer; }
        .image-item { position: relative; }
        .required-field::after { content: " *"; color: red; }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container upload-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-plus-circle"></i> Upload New Room</h3>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <?php if($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>
    
    <?php if($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $error_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>
    
    <!-- Plan Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i> 
        <strong>Plan: <?php echo $is_premium ? 'Premium (Unlimited Rooms)' : 'Free (Max 1 Rooms)'; ?></strong> | 
        Current Rooms: <?php echo $room_count; ?> of <?php echo $is_premium ? 'Unlimited' : '1'; ?>
        <?php if(!$is_premium): ?>
            <br><small>Upgrade to premium for unlimited rooms and more features.</small>
        <?php endif; ?>
    </div>
    
    <div class="upload-card">
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="required-field">Room Title</label>
                        <input type="text" name="room_title" class="form-control" 
                               value="<?php echo $_POST['room_title'] ?? ''; ?>" 
                               placeholder="e.g., Deluxe Double Room, Executive Suite, Family Room" required>
                        <small class="text-muted">Give your room a descriptive name</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Describe room features, view, amenities, bed size, facilities..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                        <small class="text-muted">Help guests understand what makes your room special</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required-field">Capacity (Persons)</label>
                                <input type="number" name="capacity" class="form-control" 
                                       value="<?php echo $_POST['capacity'] ?? '2'; ?>" 
                                       min="1" max="10" required>
                                <small class="text-muted">How many persons can stay?</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required-field">Number of Rooms</label>
                                <input type="number" name="room_count" class="form-control" 
                                       value="<?php echo $_POST['room_count'] ?? '1'; ?>" 
                                       min="1" max="50" required>
                                <small class="text-muted">How many identical rooms?</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required-field">Price per Night (৳)</label>
                                <input type="number" name="price_per_night" class="form-control" 
                                       value="<?php echo $_POST['price_per_night'] ?? ''; ?>" 
                                       min="500" step="100" required>
                                <small class="text-muted">Room price per night</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required-field">Room Images</label>
                        <div class="custom-file">
                            <input type="file" name="images[]" class="custom-file-input" 
                                   id="imageInput" multiple accept="image/*" required 
                                   onchange="previewImages(event)">
                            <label class="custom-file-label" for="imageInput">
                                Choose up to 3 images
                            </label>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Max 3 images, 2MB each. First image will be primary.
                        </small>
                        
                        <div class="image-preview-container" id="imagePreviewContainer">
                            <!-- Preview will appear here -->
                        </div>
                        
                        <div class="mt-2">
                            <small id="imageCount" class="text-muted">No images selected</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-upload"></i> Upload Room
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                    
                    
                </div>
            </div>
        </form>
    </div>
    
   

<script>
let selectedImages = [];

function previewImages(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imageCount = document.getElementById('imageCount');
    
    previewContainer.innerHTML = '';
    selectedImages = [];
    
    // Show first 3 images only
    for (let i = 0; i < Math.min(files.length, 3); i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            selectedImages.push({
                src: e.target.result,
                name: file.name
            });
            
            updatePreview();
        }
        
        reader.readAsDataURL(file);
    }
    
    imageCount.textContent = files.length > 3 
        ? `3 of ${files.length} images selected (max 3)` 
        : `${files.length} image(s) selected`;
}

function updatePreview() {
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = '';
    
    selectedImages.forEach((image, index) => {
        const imageItem = document.createElement('div');
        imageItem.className = 'image-item';
        imageItem.innerHTML = `
            <img src="${image.src}" class="image-preview" alt="Preview ${index + 1}">
            <div class="remove-image" onclick="removeImage(${index})">×</div>
        `;
        previewContainer.appendChild(imageItem);
    });
}

function removeImage(index) {
    selectedImages.splice(index, 1);
    updatePreview();
    
    // Update file input
    const dataTransfer = new DataTransfer();
    // This is a simplified version - in real implementation, 
    // you might need to recreate the file input
}

// Update file input label
document.getElementById('imageInput').addEventListener('change', function(e) {
    const fileName = e.target.files.length > 1 
        ? `${e.target.files.length} files selected` 
        : e.target.files[0]?.name || 'Choose file';
    
    const label = this.nextElementSibling;
    label.textContent = fileName;
});

// Form validation
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const price = document.querySelector('input[name="price_per_night"]').value;
    if (price < 500) {
        alert('Minimum price per night is ৳500');
        e.preventDefault();
        return false;
    }
    
    const images = document.getElementById('imageInput').files;
    if (images.length === 0) {
        alert('Please upload at least one room image');
        e.preventDefault();
        return false;
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>