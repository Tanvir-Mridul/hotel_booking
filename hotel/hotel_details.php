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
?>

<link rel="stylesheet" href="../style.css">
<div class="container mt-5">
    <div class="row">

        <!-- Hotel Image -->
        <div class="col-md-6">
            <img src="../uploads/<?php echo $hotel['image']; ?>" class="img-fluid rounded">
        </div>

        <!-- Hotel Info -->
        <div class="col-md-6">
            <h2><?php echo $hotel['hotel_name']; ?></h2>
            <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
            <p><?php echo $hotel['description']; ?></p>
            <h4 class="text-success">
                ৳ <?php echo $hotel['price']; ?> / night
            </h4>
            <hr>

            <!-- Booking Section -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                <form action="book_now.php" method="POST" id="bookingForm" onsubmit="return validateDate()">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                    
                    <div class="mb-3">
                        <label><strong>Select Booking Date</strong></label>
                        <input type="date" 
                               name="booking_date" 
                               id="bookingDate" 
                               class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>" 
                               required>
                        <small class="text-muted">Select a date for your stay</small>
                        <div id="dateMessage" class="mt-2" style="font-size: 14px;"></div>
                    </div>
                    
                    <button type="submit" id="bookBtn" class="btn btn-primary">Book Now</button>
                </form>

                <script>
                function validateDate() {
                    const selectedDate = document.getElementById('bookingDate').value;
                    const today = new Date().toISOString().split('T')[0];
                    
                    if (selectedDate < today) {
                        alert('❌ Cannot book past dates!');
                        return false;
                    }
                    
                    return true;
                }

                // Set minimum date to today
                document.getElementById('bookingDate').min = new Date().toISOString().split('T')[0];
                </script>
            <?php else: ?>
                <a href="../login.php" class="btn btn-warning">
                    Login to Book
                </a>
            <?php endif; ?>
            
        </div>
    </div>
    <br><br><br><br>
</div>

<?php include "../footer.php"; ?>