<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include "../db_connect.php";
include "../header.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM bookings WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-5">
    <h2>My Bookings</h2>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Hotel</th>
                <th>Location</th>
                <th>Price</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php $i=1; while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $row['hotel_name']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>৳ <?php echo $row['price']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No bookings yet</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>

<?php include "../footer.php"; ?>
