<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get hotel ID
$hotel_sql = "SELECT id, hotel_name FROM hotels WHERE owner_id='$owner_id'";
$hotel_result = mysqli_query($conn, $hotel_sql);
$hotel = mysqli_fetch_assoc($hotel_result);
$hotel_id = $hotel['id'];

// Handle date actions
if (isset($_POST['action'])) {
    $date = $_POST['date'];
    $room_id = $_POST['room_id'] ?? 0;
    $action = $_POST['action'];
    
    if ($action == 'block') {
        // Check if already blocked
        $check_sql = "SELECT id FROM blocked_dates 
                      WHERE hotel_id='$hotel_id' 
                      AND blocked_date='$date'
                      AND (room_id='$room_id' OR room_id=0)";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) == 0) {
            mysqli_query($conn, "INSERT INTO blocked_dates (hotel_id, room_id, blocked_date, reason) 
                                VALUES ('$hotel_id', '$room_id', '$date', 'Owner blocked')");
        }
    } 
    elseif ($action == 'unblock') {
        mysqli_query($conn, "DELETE FROM blocked_dates 
                            WHERE hotel_id='$hotel_id' 
                            AND blocked_date='$date'
                            AND (room_id='$room_id' OR room_id=0)");
    }
    
    header("Location: manage_dates.php?msg=updated");
    exit();
}

// Get rooms for this hotel
$rooms_sql = "SELECT id, room_title FROM rooms WHERE hotel_id='$hotel_id'";
$rooms_result = mysqli_query($conn, $rooms_sql);

// Get blocked dates
$blocked_sql = "SELECT * FROM blocked_dates 
                WHERE hotel_id='$hotel_id' 
                ORDER BY blocked_date DESC";
$blocked_result = mysqli_query($conn, $blocked_sql);

// Get booked dates with room info
$booked_sql = "SELECT 
                b.id as booking_id,
                b.check_in_date,
                b.check_out_date,
                b.status,
                r.id as room_id,
                r.room_title,
                u.name as customer_name
               FROM bookings b
               JOIN rooms r ON b.room_id = r.id
               JOIN users u ON b.user_id = u.id
               WHERE r.hotel_id='$hotel_id' 
               AND b.status='confirmed'
               ORDER BY b.check_in_date";
$booked_result = mysqli_query($conn, $booked_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Dates</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .container { max-width: 1200px; }
        .date-card { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        }
        .date-box { 
            display: inline-block; 
            background: #ff6b6b; 
            color: white; 
            padding: 8px 15px; 
            border-radius: 20px; 
            margin: 5px; 
            font-size: 14px;
        }
        .date-box.hotel { background: #ff6b6b; }
        .date-box.room { background: #ff9f43; }
        .date-box.booked { background: #6c5ce7; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .booked-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #6c5ce7;
        }
        .room-badge {
            background: #3498db;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
<h3 class="mb-4">📅 Manage Blocked Dates</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Dates updated successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Block New Date -->
    <div class="date-card">
        <h5><i class="fas fa-ban"></i> Block New Date</h5>
        
        <form method="POST" class="row align-items-end">
            <div class="col-md-3">
                <label>Select Date</label>
                <input type="date" name="date" class="form-control" 
                       min="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="col-md-4">
                <label>Select Room (Optional)</label>
                <select name="room_id" class="form-control">
                    <option value="0">-- Entire Hotel --</option>
                    <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                    <option value="<?= $room['id'] ?>"><?= $room['room_title'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <input type="hidden" name="action" value="block">
                <button type="submit" class="btn btn-danger btn-block">
                    <i class="fas fa-ban"></i> Block Date
                </button>
            </div>
        </form>
        
        <p class="text-muted mt-2 mb-0">
            <small>
                <i class="fas fa-info-circle"></i> 
                Select "Entire Hotel" to block for all rooms, or select specific room.
            </small>
        </p>
    </div>
    
    <!-- Blocked Dates List -->
    <div class="date-card">
        <h5><i class="fas fa-calendar-times"></i> Blocked Dates</h5>
        
        <?php 
        // Reset rooms result
        mysqli_data_seek($rooms_result, 0);
        $rooms = [];
        while($r = mysqli_fetch_assoc($rooms_result)) {
            $rooms[$r['id']] = $r['room_title'];
        }
        ?>
        
        <?php if(mysqli_num_rows($blocked_result) > 0): ?>
            <div class="mb-3">
                <?php while($blocked = mysqli_fetch_assoc($blocked_result)): 
                    $date = $blocked['blocked_date'];
                    $room_id = $blocked['room_id'];
                    $is_hotel = ($room_id == 0);
                ?>
                    <div class="date-box <?= $is_hotel ? 'hotel' : 'room' ?>">
                        <?= date('d M Y', strtotime($date)) ?>
                        
                        <?php if($is_hotel): ?>
                            <small>(Entire Hotel)</small>
                        <?php else: ?>
                            <small>(<?= $rooms[$room_id] ?? 'Room' ?>)</small>
                        <?php endif; ?>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="date" value="<?= $date ?>">
                            <input type="hidden" name="room_id" value="<?= $room_id ?>">
                            <input type="hidden" name="action" value="unblock">
                            <button type="submit" class="btn btn-sm btn-light" 
                                    onclick="return confirm('Unblock <?= $date ?>?')">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No dates blocked yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Booked Dates Info -->
    <div class="date-card">
        <h5><i class="fas fa-calendar-check"></i> Already Booked Dates (Auto-blocked)</h5>
        
        <?php if(mysqli_num_rows($booked_result) > 0): ?>
            <p class="text-muted mb-3">These rooms are booked on the following dates:</p>
            
            <?php while($booking = mysqli_fetch_assoc($booked_result)): 
                $check_in = $booking['check_in_date'];
                $check_out = $booking['check_out_date'];
                $room_title = $booking['room_title'];
                $customer_name = $booking['customer_name'];
                $booking_id = $booking['booking_id'];
            ?>
                <div class="booked-info mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>
                                <span class="room-badge"><?= $room_title ?></span>
                                Booking #<?= $booking_id ?>
                            </strong>
                            <span class="text-muted ml-2">
                                <i class="fas fa-user"></i> <?= $customer_name ?>
                            </span>
                        </div>
                        <span class="badge badge-success">Confirmed</span>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-calendar-day"></i> 
                            <?= date('d M Y', strtotime($check_in)) ?> - 
                            <?= date('d M Y', strtotime($check_out)) ?>
                        </small>
                    </div>
                    
                    <div>
                        <?php 
                        // Generate dates between check-in and check-out
                        $current = strtotime($check_in);
                        $end = strtotime($check_out);
                        
                        while($current < $end) {
                            $date = date('Y-m-d', $current);
                        ?>
                            <span class="date-box booked">
                                <?= date('d M', $current) ?>
                            </span>
                        <?php
                            $current = strtotime('+1 day', $current);
                        }
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">No confirmed bookings yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Room Availability Calendar (Optional) -->
    <div class="date-card">
        <h5><i class="fas fa-calendar-alt"></i> Room Availability Overview</h5>
        
        <div class="row">
            <?php 
            // Get all rooms
            mysqli_data_seek($rooms_result, 0);
            $room_counter = 0;
            
            while($room = mysqli_fetch_assoc($rooms_result)): 
                $room_id = $room['id'];
                $room_title = $room['room_title'];
                $room_counter++;
                
                // Get booked dates for this room
                $room_booked_sql = "SELECT check_in_date, check_out_date 
                                   FROM bookings 
                                   WHERE room_id='$room_id' 
                                   AND status='confirmed' 
                                   LIMIT 3";
                $room_booked_result = mysqli_query($conn, $room_booked_sql);
            ?>
                <div class="col-md-6 mb-3">
                    <div class="border p-3 rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-door-closed"></i> <?= $room_title ?>
                        </h6>
                        
                        <?php if(mysqli_num_rows($room_booked_result) > 0): ?>
                            <small class="text-muted d-block mb-2">Next bookings:</small>
                            <ul class="list-unstyled mb-0">
                                <?php while($room_booking = mysqli_fetch_assoc($room_booked_result)): ?>
                                    <li class="mb-1">
                                        <small>
                                            <i class="fas fa-calendar-check text-primary"></i>
                                            <?= date('d M', strtotime($room_booking['check_in_date'])) ?> - 
                                            <?= date('d M', strtotime($room_booking['check_out_date'])) ?>
                                        </small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> No upcoming bookings
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($room_counter % 2 == 0): ?>
                    <div class="w-100"></div>
                <?php endif; ?>
                
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>