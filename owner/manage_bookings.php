<?php
session_start();
include "../db_connect.php";
 include "../header.php"; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

$sql = "SELECT b.*, u.name AS user_name, h.hotel_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN hotels h ON b.hotel_id = h.id
WHERE b.owner_id = '$owner_id'
ORDER BY b.id DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Bookings</title>

  <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>

</head>

<body class="bg-light">
    <?php include "../header.php"; ?>
<div class="container mt-4">
<h4>My Bookings</h4>

<table class="table table-bordered bg-white">
<tr>
<th>ID</th>
<th>Hotel</th>
<th>User</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['hotel_name']; ?></td>
<td><?= $row['user_name']; ?></td>
<td>৳ <?= $row['price']; ?></td>

<td>
<span class="badge 
<?= $row['status']=='confirmed'?'badge-success':($row['status']=='cancelled'?'badge-danger':'badge-warning'); ?>">
<?= $row['status']; ?>
</span>
</td>

<td>
<?php if($row['status']=='pending'): ?>
<a href="update_booking.php?id=<?= $row['id']; ?>&action=confirm" class="btn btn-success btn-sm">Confirm</a>
<a href="update_booking.php?id=<?= $row['id']; ?>&action=cancel" class="btn btn-danger btn-sm">Cancel</a>
<?php endif; ?>

<a href="delete_booking.php?id=<?= $row['id']; ?>" 
   class="btn btn-dark btn-sm"
   onclick="return confirm('Delete booking?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>
