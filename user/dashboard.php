<?php
session_start();

// login protection
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include "../header.php";
?>

<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>

    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>My Bookings</h4>
                <p>View all your bookings</p>
                <a href="my_booking.php" class="btn btn-primary">View</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Browse Hotels</h4>
                <p>Find hotels & book rooms</p>
                <a href="../hotel/hotel_list.php" class="btn btn-success">Browse</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Logout</h4>
                <p>Securely logout</p>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

    </div>
</div>


