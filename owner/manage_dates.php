<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Handle date blocking/unblocking
if (isset($_POST['action'])) {
    $hotel_id = $_POST['hotel_id'];
    $date = $_POST['date'];
    
    // Get current booked dates
    $sql = "SELECT booked_dates FROM hotels WHERE id=? AND owner_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $hotel_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $booked_dates = json_decode($row['booked_dates'] ?? '[]', true);
        
        if ($_POST['action'] == 'block') {
            // Add date to blocked dates
            if (!in_array($date, $booked_dates)) {
                $booked_dates[] = $date;
            }
        } elseif ($_POST['action'] == 'unblock') {
            // Remove date from blocked dates
            $booked_dates = array_diff($booked_dates, [$date]);
        }
        
        // Update database
        $new_dates = json_encode(array_values(array_unique($booked_dates)));
        $update_sql = "UPDATE hotels SET booked_dates=? WHERE id=? AND owner_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sii", $new_dates, $hotel_id, $owner_id);
        $update_stmt->execute();
        
        echo "<script>alert('Date updated successfully!');</script>";
    }
}

// Get owner's hotels
$hotels_sql = "SELECT id, hotel_name, booked_dates FROM hotels WHERE owner_id=? ORDER BY id DESC";
$hotels_stmt = $conn->prepare($hotels_sql);
$hotels_stmt->bind_param("i", $owner_id);
$hotels_stmt->execute();
$hotels_result = $hotels_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Booking Dates</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 20px; }
        .container { max-width: 1000px; }
        .hotel-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .date-box { display: inline-block; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 5px; margin: 5px; }
        .date-box.available { background: #51cf66; }
    </style>
    <?php include "../header.php"; ?>
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">
    <h3 class="mb-4">📅 Manage Booking Dates</h3>
    
    <?php while($hotel = $hotels_result->fetch_assoc()): ?>
    <div class="hotel-card">
        <h5><?= $hotel['hotel_name'] ?></h5>
        <p class="text-muted">Hotel ID: #<?= $hotel['id'] ?></p>
        
        <!-- Block New Date -->
        <form method="POST" class="mb-3 row">
            <div class="col-md-4">
                <input type="date" 
                       name="date" 
                       class="form-control" 
                       min="<?= date('Y-m-d') ?>" 
                       required>
            </div>
            <div class="col-md-4">
                <input type="hidden" name="hotel_id" value="<?= $hotel['id'] ?>">
                <button type="submit" name="action" value="block" class="btn btn-danger">
                    <i class="fas fa-ban"></i> Block Date
                </button>
            </div>
        </form>
        
        <!-- Booked/Blocked Dates -->
        <h6>Blocked Dates:</h6>
        <?php 
        $booked_dates = json_decode($hotel['booked_dates'] ?? '[]', true);
        if (count($booked_dates) > 0): 
        ?>
            <?php foreach($booked_dates as $date): ?>
            <div class="date-box">
                <?= date('d M Y', strtotime($date)) ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="hotel_id" value="<?= $hotel['id'] ?>">
                    <input type="hidden" name="date" value="<?= $date ?>">
                    <button type="submit" name="action" value="unblock" class="btn btn-sm btn-light" 
                            onclick="return confirm('Unblock <?= $date ?>?')">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No dates blocked yet</p>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>