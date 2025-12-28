<?php
session_start();
include "../db_connect.php";

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
ORDER BY b.id DESC";

$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>Manage Bookings</h3>

    <table class="table table-bordered bg-white">
        <tr>
            <th>ID</th>
            <th>Hotel</th><?php



if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

$sql = "SELECT * FROM bookings 
        WHERE owner_id = '$owner_id' 
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>

    <!-- Bootstrap 4 CSS -->
    
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
    <h3 class="mb-4">Manage Bookings</h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hotel</th>
                        <th>User ID</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['hotel_name']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td>৳ <?php echo $row['price']; ?></td>

                        <td>
                            <?php
                            if($row['status'] == 'confirmed'){
                                echo '<span class="badge badge-success">confirmed</span>';
                            } elseif($row['status'] == 'cancelled'){
                                echo '<span class="badge badge-danger">cancelled</span>';
                            } else {
                                echo '<span class="badge badge-warning">pending</span>';
                            }
                            ?>
                        </td>

                        <td>
                        <?php if($row['status'] == 'pending'): ?>
                            <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=confirm"
                               class="btn btn-success btn-sm">
                               Confirm
                            </a>

                            <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=cancel"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Cancel booking?')">
                               Cancel
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No bookings found
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

            <th>User ID</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if(mysqli_num_rows($result)>0): ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['hotel_name']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td>৳ <?php echo $row['price']; ?></td>

                <td>
                    <span class="badge 
                        <?php
                        if($row['status']=='confirmed') echo 'bg-success';
                        elseif($row['status']=='cancelled') echo 'bg-danger';
                        else echo 'bg-warning';
                        ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>

                <td>
                <?php if($row['status']=='pending'): ?>
                    <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=confirm"
                       class="btn btn-success btn-sm">Confirm</a>

                    <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=cancel"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Cancel booking?')">Cancel</a>
                <?php else: ?>
                    —
                <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No bookings found</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
