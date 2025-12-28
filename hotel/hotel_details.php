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
                <form action="book_now.php" method="POST">

                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                    <input type="hidden" name="hotel_name" value="<?php echo $hotel['hotel_name']; ?>">
                    <input type="hidden" name="location" value="<?php echo $hotel['location']; ?>">
                    <input type="hidden" name="price" value="<?php echo $hotel['price']; ?>">

                    <div class="mb-3">
                        <label>Booking Date</label>
                        <input type="date" name="booking_date" class="form-control" required>
                    </div>

                    <button class="btn btn-primary">Book Now</button>
                </form>
            <?php else: ?>
                <a href="../login.php" class="btn btn-warning">
                    Login to Book
                </a>
            <?php endif; ?>
        </div>

    </div>
    <br>
    <br>
    <br>
    <br>
</div>

<?php include "../footer.php"; ?>
