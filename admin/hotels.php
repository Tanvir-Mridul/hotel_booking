<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle status toggle
if(isset($_GET['toggle_status'])) {
    $id = $_GET['id'];
    $current_status = $_GET['current'];
    
    if($current_status == 'approved') {
        $new_status = 'off';
    } else {
        $new_status = 'approved';
    }
    
    $toggle_sql = "UPDATE hotels SET status='$new_status' WHERE id='$id'";
    mysqli_query($conn, $toggle_sql);
    header("Location: hotels.php");
}

// Get all hotels
$sql = "SELECT hotels.*, users.name AS owner_name 
        FROM hotels 
        LEFT JOIN users ON hotels.owner_id = users.id
        ORDER BY 
        CASE 
            WHEN hotels.status = 'pending' THEN 1
            WHEN hotels.status = 'approved' THEN 2
            WHEN hotels.status = 'off' THEN 3
            ELSE 4
        END, hotels.id DESC";
$result = mysqli_query($conn, $sql);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Hotels - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; width: 100%; }
        .table-box { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-off { background: #6c757d; color: white; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .filter-buttons { margin-bottom: 15px; }
        .filter-btn { margin-right: 5px; margin-bottom: 5px; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3>Manage Hotels</h3>
    
    <!-- Filter Buttons -->
    <div class="filter-buttons">
        <a href="hotels.php" class="btn btn-secondary btn-sm filter-btn">All</a>
        <a href="hotels.php?status=pending" class="btn btn-warning btn-sm filter-btn">Pending (<?php 
            $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM hotels WHERE status='pending'"))['count'];
            echo $pending_count;
        ?>)</a>
        <a href="hotels.php?status=approved" class="btn btn-success btn-sm filter-btn">Approved</a>
        <a href="hotels.php?status=off" class="btn btn-dark btn-sm filter-btn">Off</a>
        <a href="hotels.php?status=rejected" class="btn btn-danger btn-sm filter-btn">Rejected</a>
    </div>
    
    <!-- Hotels Table -->
    <div class="table-box">
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php 
                if($_GET['msg'] == 'approved') echo "Hotel approved successfully!";
                if($_GET['msg'] == 'rejected') echo "Hotel rejected successfully!";
                if($_GET['msg'] == 'toggled') echo "Status changed successfully!";
                ?>
            </div>
        <?php endif; ?>
        
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Hotel Name</th>
                <th>Owner</th>
                <th>Location</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <strong><?php echo $row['hotel_name']; ?></strong>
                        <?php if(!empty($row['image']) && $row['image'] != 'default.jpg'): ?>
                            <br>
                            <img src="../uploads/<?php echo $row['image']; ?>" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; margin-top: 5px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['owner_name']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>৳ <?php echo $row['price']; ?></td>
                    <td>
                        <?php 
                        $badge_class = 'badge-pending';
                        $status_text = 'Pending';
                        
                        if($row['status'] == 'approved') {
                            $badge_class = 'badge-approved';
                            $status_text = 'Approved ✅';
                        } 
                        if($row['status'] == 'off') {
                            $badge_class = 'badge-off';
                            $status_text = 'Off 🔴';
                        }
                        if($row['status'] == 'rejected') {
                            $badge_class = 'badge-rejected';
                            $status_text = 'Rejected';
                        }
                        ?>
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <?php if($row['status'] == 'pending'): ?>
                                <a href="approve_hotel.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-success btn-sm">✅ Approve</a>
                                <a href="reject_hotel.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm">❌ Reject</a>
                            <?php endif; ?>
                            
                            <?php if($row['status'] == 'approved'): ?>
                                <a href="?toggle_status=1&id=<?php echo $row['id']; ?>&current=approved" 
                                   class="btn btn-warning btn-sm">🔴 Turn Off</a>
                            <?php endif; ?>
                            
                            <?php if($row['status'] == 'off'): ?>
                                <a href="?toggle_status=1&id=<?php echo $row['id']; ?>&current=off" 
                                   class="btn btn-success btn-sm">✅ Turn On</a>
                            <?php endif; ?>
                            
                            <a href="delete_hotel.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-dark btn-sm"
                               onclick="return confirm('Delete this hotel permanently?')">🗑️ Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hotels found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>