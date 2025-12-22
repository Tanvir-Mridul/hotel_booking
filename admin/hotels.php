<?php
session_start();
include "../db_connect.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include "../header.php";

// Pending hotels
$sql = "SELECT hotels.*, users.name AS owner_name 
        FROM hotels 
        JOIN users ON hotels.owner_id = users.id
        ORDER BY hotels.id DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container mt-5">
    <h2>Hotel Approval</h2>

    <table class="table table-bordered mt-3">
        <tr>
            <th>Hotel</th>
            <th>Owner</th>
            <th>Location</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['hotel_name']; ?></td>
            <td><?php echo $row['owner_name']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td>৳ <?php echo $row['price']; ?></td>
            <td>
                <?php if($row['status']=='approved'): ?>
                    <span class="text-success">Approved</span>
                <?php else: ?>
                    <span class="text-warning">Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if($row['status']!='approved'): ?>
                    <a href="approve_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="reject_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>

<?php include "../footer.php"; ?>
