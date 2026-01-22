<?php
session_start();
include "../db_connect.php";
include "../header.php";

// 1️⃣ id check
if (!isset($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Hotel not found!</div></div>";
    include "../footer.php";
    exit();
}

$hotel_id = $_GET['id'];

// 2️⃣ DB থেকে hotel data আনা
$sql = "SELECT * FROM hotels WHERE id='$hotel_id' AND status='approved'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Hotel not available!</div></div>";
    include "../footer.php";
    exit();
}

$hotel = mysqli_fetch_assoc($result);

// 3️⃣ Hotel এর ACTIVE rooms আনা - শুধু active room show হবে
$rooms_sql = "SELECT r.*, 
              (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as primary_image
              FROM rooms r 
              WHERE r.hotel_id = '$hotel_id' 
              AND r.active = 1  -- শুধু active room show হবে
              ORDER BY r.discount_price ASC";
$rooms_result = mysqli_query($conn, $rooms_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $hotel['hotel_name']; ?> - Hotel Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: #f8f9fa; }
        .hotel-header { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .hotel-image { width: 100%; height: 300px; object-fit: cover; border-radius: 10px; }
        .room-card { background: white; border-radius: 10px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .room-card:hover { transform: translateY(-5px); }
        .room-image { width: 100%; height: 200px; object-fit: cover; }
        .room-price { font-size: 24px; font-weight: bold; color: #27ae60; }
        .capacity-badge { background: #3498db; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        .amenities-list { list-style: none; padding: 0; }
        .amenities-list li { margin-bottom: 5px; }
        .amenities-list i { color: #27ae60; margin-right: 8px; }
        .no-rooms { text-align: center; padding: 50px; background: white; border-radius: 10px; }
        .room-status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            z-index: 1;
        }
        .status-available { background: #28a745; color: white; }
        .status-booked { background: #ffc107; color: #212529; }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container mt-4">
    <!-- Hotel Information -->
    <div class="hotel-header">
        <div class="row">
            <div class="col-md-4">
                <img src="../uploads/<?php echo !empty($hotel['image']) ? $hotel['image'] : 'default.jpg'; ?>" 
                     class="hotel-image" alt="<?php echo $hotel['hotel_name']; ?>"
                     onerror="this.src='../assets/img/default.jpg'">
            </div>
            <div class="col-md-8">
                <h2><?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>
                <p class="text-muted mb-3">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?>
                </p>
                
                <?php if(!empty($hotel['description'])): ?>
                <div class="mb-4">
                    <h5>About Hotel</h5>
                    <p><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div style="font-size: 24px; color: #3498db;">
                                <i class="fas fa-bed"></i>
                            </div>
                            <?php $active_rooms = mysqli_num_rows($rooms_result); ?>
                            <h5 class="mt-2">
                                <?php echo $active_rooms; ?> Rooms
                            </h5>
                            <small>Available Rooms</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div style="font-size: 24px; color: #e74c3c;">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="mt-2">Verified</h5>
                            <small>Hotel Status</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div style="font-size: 24px; color: #9b59b6;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h5 class="mt-2">24/7</h5>
                            <small>Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Available Rooms -->
    <h3 class="mb-4">Available Rooms</h3>
    
    <?php 
    // Reset result pointer
    mysqli_data_seek($rooms_result, 0);
    $total_active_rooms = mysqli_num_rows($rooms_result);
    ?>
    
    <?php if($total_active_rooms > 0): ?>
        <p class="text-muted mb-4">
            Showing <?php echo $total_active_rooms; ?> available room(s)
        </p>
        
        <div class="row">
            <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                <?php
// ===== GET AMENITIES FOR THIS ROOM =====
$amenities_q = mysqli_query($conn, "
    SELECT a.name 
    FROM room_amenities ra
    JOIN amenities a ON ra.amenity_id = a.id
    WHERE ra.room_id = '{$room['id']}'
");
?>


            <div class="col-md-4 mb-4">
                <div class="room-card">
                    <!-- Room Image with Status Badge -->
                    <div style="position: relative;">
                        <div class="room-status-badge status-available">
                            Available
                        </div>
                        
                        <?php if(!empty($room['primary_image'])): ?>
                            <img src="../uploads/rooms/<?php echo $room['primary_image']; ?>" 
                                 class="room-image" 
                                 alt="<?php echo htmlspecialchars($room['room_title']); ?>"
                                 onerror="this.src='../assets/img/default-room.jpg'; this.style.padding='40px'; this.style.background='#f8f9fa';">
                        <?php else: ?>
                            <div class="room-image bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-bed fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Price Tag -->
                        

                        

<?php if(!empty($room['discount_price']) && $room['discount_price'] < $room['price_per_night']): ?>
     <div style="position: absolute; top: 15px; right: 15px; background: rgba(39, 174, 96, 0.9); color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">
        <span style="text-decoration: line-through; ">
            ৳ <?php echo number_format($room['price_per_night'], ); ?>
        </span>
        <span class=" font-weight-bold ml-2">
            ৳ <?php echo number_format($room['discount_price'], ); ?>/night
        </span>
    </div>
<?php else: ?>
    <div style="position: absolute; top: 15px; right: 15px; background: rgba(39, 174, 96, 0.9); color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">
        ৳ <?php echo number_format($room['price_per_night'], ); ?>/night
    </div>
<?php endif; ?>



                    </div>
                    
                    <!-- Room Details -->
                    <div class="p-3">
                        <h5><?php echo htmlspecialchars($room['room_title']); ?></h5>
                        
                        <div class="mb-2">
                            <span class="capacity-badge">
                                <i class="fas fa-users"></i> <?php echo $room['capacity']; ?> Persons
                            </span>
                            <?php if($room['room_count'] > 1): ?>
                                <span class="badge badge-light ml-2">
                                    <i class="fas fa-door-closed"></i> <?php echo $room['room_count']; ?> Rooms
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(!empty($room['description'])): ?>
                        <p class="text-muted mb-3" style="font-size: 14px;">
                            <?php echo substr(htmlspecialchars($room['description']), 0, 100); ?>...
                        </p>
                        <?php endif; ?>
                        
                        <!-- Amenities -->
                       <!-- Amenities -->
<!-- Amenities -->
<?php if(mysqli_num_rows($amenities_q) > 0): ?>
<div class="row mt-2">
    <?php while($am = mysqli_fetch_assoc($amenities_q)): 
        $icon = "fa-check"; // default icon

        // ICON MAPPING
        if (stripos($am['name'], 'wifi') !== false) $icon = "fa-wifi";
        elseif (stripos($am['name'], 'tv') !== false) $icon = "fa-tv";
        elseif (stripos($am['name'], 'ac') !== false || stripos($am['name'], 'air') !== false) $icon = "fa-snowflake";
        elseif (stripos($am['name'], 'bath') !== false) $icon = "fa-bath";
        elseif (stripos($am['name'], 'parking') !== false) $icon = "fa-car";
        elseif (stripos($am['name'], 'service') !== false) $icon = "fa-concierge-bell";
    ?>
        <div class="col-6">
            <div style="font-size:14px; margin-bottom:6px;">
                <i class="fas <?php echo $icon; ?> text-success"></i>
                <?php echo htmlspecialchars($am['name']); ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<?php else: ?>
    <p class="text-muted" style="font-size:13px;">No amenities listed</p>
<?php endif; ?>


                        
                        <!-- Action Buttons -->
                        <div class="mt-3">
                            <a href="room_details.php?room_id=<?php echo $room['id']; ?>&hotel_id=<?php echo $hotel_id; ?>" 
                               class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> View Details & Book
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-rooms">
            <i class="fas fa-bed fa-4x text-muted mb-3"></i>
            <h4>No Rooms Available</h4>
            <p class="text-muted">Currently all rooms are inactive or under maintenance.</p>
            <a href="../hotel/hotel_list.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Browse Other Hotels
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../footer.php"; ?>
</body>
</html>