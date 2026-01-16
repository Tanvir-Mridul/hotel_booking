<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_GET['room_id'])) {
    header("Location: hotel_list.php");
    exit();
}

$room_id = intval($_GET['room_id']);

// Get room details - শুধু active rooms দেখাবে
$room_sql = "SELECT r.*, h.hotel_name, h.location, h.description as hotel_desc, 
             u.name as owner_name, u.phone as owner_phone
             FROM rooms r
             JOIN hotels h ON r.hotel_id = h.id
             JOIN users u ON h.owner_id = u.id
             WHERE r.id = $room_id AND r.active = 1"; // শুধুমাত্র active rooms
$room_result = mysqli_query($conn, $room_sql);

if (mysqli_num_rows($room_result) == 0) {
    
    echo "<div class='container mt-5'>
            <div class='alert alert-warning'>Room not available!</div>
            <a href='hotel_list.php' class='btn btn-primary'>Browse Hotels</a>
          </div>";
    
    exit();
}

$room = mysqli_fetch_assoc($room_result);

// Get room images
$images_sql = "SELECT * FROM room_images WHERE room_id = '$room_id' ORDER BY is_primary DESC";
$images_result = mysqli_query($conn, $images_sql);
$images = [];
while ($img = mysqli_fetch_assoc($images_result)) {
    $images[] = $img;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $room['room_title']; ?> - Room Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: #f8f9fa; }
        .room-container { max-width: 1000px; margin: auto; }
        .room-header { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; }
        .image-slider { position: relative; }
        .main-image { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; }
        .thumbnail-container { display: flex; gap: 10px; margin-top: 15px; }
        .thumbnail { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid transparent; }
        .thumbnail.active { border-color: #3498db; }
        .booking-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); }
        .price-display { font-size: 32px; font-weight: bold; color: #27ae60; }
        .amenity-icon { font-size: 20px; color: #3498db; margin-right: 10px; }
    </style>
    <?php include "../header.php"; ?>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container room-container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="hotel_list.php">Hotels</a></li>
            <li class="breadcrumb-item"><a href="hotel_details.php?id=<?php echo $room['hotel_id']; ?>">
                <?php echo $room['hotel_name']; ?>
            </a></li>
            <li class="breadcrumb-item active"><?php echo $room['room_title']; ?></li>
        </ol>
    </nav>
    
    <div class="room-header">
        <div class="row">
            <div class="col-md-8">
                <h2><?php echo $room['room_title']; ?></h2>
                <p class="text-muted">
                    <i class="fas fa-hotel"></i> <?php echo $room['hotel_name']; ?> | 
                    <i class="fas fa-map-marker-alt"></i> <?php echo $room['location']; ?>
                </p>

                <!-- Image Gallery -->
    <?php if(count($images) > 0): ?>
    <div class="mb-4">
        <h4>Room Photos</h4>
        <div class="image-slider">
            <img id="mainImage" src="../uploads/rooms/<?php echo $images[0]['image_url']; ?>" 
                 class="main-image" alt="Room Image"
                 onerror="this.src='../assets/img/default.jpg'">
            
            <?php if(count($images) > 1): ?>
            <div class="thumbnail-container">
                <?php foreach($images as $index => $image): ?>
                <img src="../uploads/rooms/<?php echo $image['image_url']; ?>" 
                     class="thumbnail <?php echo $index == 0 ? 'active' : ''; ?>" 
                     alt="Thumbnail <?php echo $index + 1; ?>"
                     onclick="changeImage(this.src)"
                     onerror="this.src='../assets/img/default.jpg'">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
                
                <!-- Room Features -->
                <div class="row mb-4">
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="amenity-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div><strong><?php echo $room['capacity']; ?></strong></div>
                            <small>Persons</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="amenity-icon">
                                <i class="fas fa-door-closed"></i>
                            </div>
                            <div><strong><?php echo $room['room_count']; ?></strong></div>
                            <small>Rooms</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="amenity-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div><strong>Double</strong></div>
                            <small>Bed Type</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="amenity-icon">
                                <i class="fas fa-ruler-combined"></i>
                            </div>
                            <div><strong>350</strong></div>
                            <small>sq.ft</small>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <?php if(!empty($room['description'])): ?>
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Amenities -->
                <div>
                    <h5>Amenities</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Free WiFi</li>
                                <li><i class="fas fa-check text-success"></i> Air Conditioning</li>
                                <li><i class="fas fa-check text-success"></i> TV with Cable</li>
                                <li><i class="fas fa-check text-success"></i> Attached Bathroom</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Room Service</li>
                                <li><i class="fas fa-check text-success"></i> Hot Water</li>
                                <li><i class="fas fa-check text-success"></i> Housekeeping</li>
                                <li><i class="fas fa-check text-success"></i> Security</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="booking-card">
                    <div class="price-display text-center mb-3">
                        ৳ <?php echo $room['price_per_night']; ?>
                        <div style="font-size: 14px; color: #666;">per night</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="badge badge-success p-2" style="font-size: 14px;">
                            <i class="fas fa-check-circle"></i> Available
                        </div>
                    </div>
                    
                    <!-- Booking Form -->
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                    <form action="book_room.php" method="POST" id="bookingForm">
                        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                        
                        <div class="form-group">
                            <label>Check-in Date *</label>
                            <input type="date" name="check_in" class="form-control" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Check-out Date *</label>
                            <input type="date" name="check_out" class="form-control" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Number of Rooms</label>
                            <select name="rooms_count" class="form-control">
                                <?php for($i = 1; $i <= min($room['room_count'], 5); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> Room(s)</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Guests</label>
                            <select name="guests" class="form-control">
                                <?php for($i = 1; $i <= min($room['capacity'], 10); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> Person(s)</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Please <a href="../login.php" class="alert-link">login</a> as user to book this room.
                    </div>
                    <a href="../login.php" class="btn btn-warning btn-block">
                        <i class="fas fa-sign-in-alt"></i> Login to Book
                    </a>
                    <?php endif; ?>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-lock"></i> Secure Booking • 
                            <i class="fas fa-shield-alt"></i> Best Price Guarantee
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
</div>

<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
    
    // Update active thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
        if (thumb.src === src) {
            thumb.classList.add('active');
        }
    });
}

// Form validation
document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    const checkIn = document.querySelector('input[name="check_in"]').value;
    const checkOut = document.querySelector('input[name="check_out"]').value;
    
    if (checkIn && checkOut && checkIn >= checkOut) {
        e.preventDefault();
        alert('Check-out date must be after check-in date!');
    }
});
</script>

<?php include "../footer.php"; ?>
</body>
</html>