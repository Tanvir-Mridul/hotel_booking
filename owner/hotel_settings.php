//hotel_setting.php//
<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$hotel = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM hotels WHERE owner_id='$owner_id'"
));

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hotel_name = mysqli_real_escape_string($conn, $_POST['hotel_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Image upload
    $image_name = $hotel['image'];
    
    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['hotel_image']['type'], $allowed_types) && 
            $_FILES['hotel_image']['size'] <= $max_size) {
            
            // Delete old image if exists
            if (!empty($image_name) && $image_name != 'default.jpg') {
                $old_image_path = "../uploads/" . $image_name;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            
            // Upload new image
            $file_ext = pathinfo($_FILES['hotel_image']['name'], PATHINFO_EXTENSION);
            $image_name = time() . "_hotel_" . $owner_id . "." . $file_ext;
            $upload_path = "../uploads/" . $image_name;
            
            if (move_uploaded_file($_FILES['hotel_image']['tmp_name'], $upload_path)) {
                // Image uploaded successfully
            } else {
                $error_msg = "Image upload failed!";
            }
        } else {
            $error_msg = "Invalid image file! Max size 2MB, only JPG/PNG allowed.";
        }
    }
    
    if (empty($error_msg)) {
        $update_sql = "UPDATE hotels SET 
                      hotel_name = '$hotel_name',
                      location = '$location',
                      description = '$description',
                      image = '$image_name'
                      WHERE owner_id = '$owner_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $success_msg = "Hotel settings updated successfully!";
            // Refresh hotel data
            $hotel = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT * FROM hotels WHERE owner_id='$owner_id'"
            ));
        } else {
            $error_msg = "Update failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hotel Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .settings-container { max-width: 800px; margin: auto; }
        .settings-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .preview-image { width: 200px; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container settings-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-cog"></i> Hotel Settings</h3>
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
    
    <div class="settings-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Hotel Name *</label>
                        <input type="text" name="hotel_name" class="form-control" 
                               value="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" class="form-control" 
                               value="<?php echo htmlspecialchars($hotel['location']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" 
                                  placeholder="Describe your hotel, facilities, amenities..."><?php echo htmlspecialchars($hotel['description'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group text-center">
                        <label>Hotel Image</label>
                        <div class="mb-3">
                            <?php if(!empty($hotel['image']) && $hotel['image'] != 'default.jpg'): ?>
                                <img src="../uploads/<?php echo $hotel['image']; ?>" 
                                     class="preview-image img-thumbnail" id="imagePreview">
                            <?php else: ?>
                                <div class="preview-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-hotel fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="hotel_image" class="form-control-file" 
                               accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">Max 2MB, JPG/PNG format</small>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <!-- Hotel Status Info -->
    <div class="settings-card mt-4">
        <h5 class="mb-3">Hotel Information</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Hotel ID:</strong> #<?php echo $hotel['id']; ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge badge-<?php echo $hotel['status'] == 'approved' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($hotel['status']); ?>
                    </span>
                </p>
                <p><strong>Created:</strong> <?php echo date('d M Y', strtotime($hotel['created_at'])); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Rooms:</strong> 
                    <?php 
                    $room_count = mysqli_fetch_assoc(mysqli_query($conn, 
                        "SELECT COUNT(*) as total FROM rooms WHERE hotel_id='{$hotel['id']}'"
                    ))['total'];
                    echo $room_count;
                    ?>
                </p>
                <p><strong>Owner ID:</strong> <?php echo $hotel['owner_id']; ?></p>
                <p><strong>Last Updated:</strong> <?php echo date('d M Y H:i', strtotime($hotel['created_at'])); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Create new image element
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image img-thumbnail';
                img.id = 'imagePreview';
                preview.parentNode.replaceChild(img, preview);
            }
        }
        
        reader.readAsDataURL(file);
    }
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>