<?php
session_start();
include "../db_connect.php";

     include "../header.php"; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

/* ===== PREMIUM CHECK ===== */
$is_premium = false;
$remaining_days = 0;

$sub_q = mysqli_query($conn, "
    SELECT end_date, DATEDIFF(end_date, CURDATE()) AS remaining_days
    FROM owner_subscriptions
    WHERE owner_id='$owner_id'
    AND status='approved'
    ORDER BY id DESC
    LIMIT 1
");

if ($sub_q && mysqli_num_rows($sub_q) > 0) {
    $sub = mysqli_fetch_assoc($sub_q);
    $remaining_days = (int)$sub['remaining_days'];

    if ($remaining_days > 0) {
        $is_premium = true;
    }
}
$featured_result = mysqli_query($conn, "SELECT COUNT(*) as featured 
    FROM hotels 
    WHERE owner_id='$owner_id' AND status='approved'
");
$featured = mysqli_fetch_assoc($featured_result)['featured'];

// Count stats
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM hotels WHERE owner_id='$owner_id'");
$total_row = mysqli_fetch_assoc($total_result);
$total_flats = $total_row['total'];

$approved_result = mysqli_query($conn, "SELECT COUNT(*) as approved FROM hotels WHERE owner_id='$owner_id' AND status='approved'");
$approved_row = mysqli_fetch_assoc($approved_result);
$approved_flats = $approved_row['approved'];

$pending_result = mysqli_query($conn, "SELECT COUNT(*) as pending FROM hotels WHERE owner_id='$owner_id' AND status='pending'");
$pending_row = mysqli_fetch_assoc($pending_result);
$pending_flats = $pending_row['pending'];

// Get all flats
$sql = "SELECT * FROM hotels WHERE owner_id='$owner_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
  
     <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    
    
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main { margin-top: 60px; padding: 20px; width: 100%; }
        
        /* Stats Cards */
        .stats { display: flex; gap: 15px; margin-bottom: 25px; }
        .stat-card { 
            flex: 1; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-number { font-size: 28px; font-weight: bold; }
        .total .stat-number { color: #3498db; }
        .approved .stat-number { color: #27ae60; }
        .pending .stat-number { color: #f39c12; }
        .stat-label { color: #666; font-size: 14px; margin-top: 5px; }
        
        /* Hotel Cards */
        .hotel-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .hotel-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .approved-badge { background: #d4edda; color: #155724; }
        .pending-badge { background: #fff3cd; color: #856404; }
        .rejected-badge { background: #f8d7da; color: #721c24; }
        
        .btn-sm { padding: 5px 10px; font-size: 13px; }
    </style>
</head>
<body>

<!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>

<!-- Main Content -->
<div class="main">



<?php if($is_premium): ?>
<!-- ⭐ PREMIUM DASHBOARD -->
<div class="card shadow mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">⭐ Premium Owner Dashboard</h5>
            <small class="text-muted">
                Subscription Active | <?= $remaining_days ?> days left
            </small>
        </div>
        <span class="badge badge-warning p-2">PREMIUM</span>
    </div>
</div>

<div class="stats">
    
<div class="stat-card total">
            <div class="stat-number"><?php echo $total_flats; ?></div>
            <div class="stat-label">Total Flats</div>
        </div>
    <div class="stat-card approved">
        <div class="stat-number"><?= $approved_flats ?></div>
        <div class="stat-label">Approved Hotels</div>
    </div>
        <div class="stat-card pending">
            <div class="stat-number"><?php echo $pending_flats; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    

    <div class="stat-card pending">
        <div class="stat-number"><?= $remaining_days ?></div>
        <div class="stat-label">Days Left</div>
    </div>
</div>
<?php else: ?>

<!-- 🚫 FREE OWNER VIEW -->
<div class="alert alert-warning d-flex justify-content-between align-items-center">
    <div>
        <strong>Premium Features Locked</strong><br>
        Feature your hotels & get analytics
    </div>
    <a href="subscription.php" class="btn btn-warning btn-sm">
        Upgrade Now
    </a>
</div>

<?php endif; ?>
   
    
    <!-- Add New Button -->
    <a href="upload_flat.php" class="btn btn-primary mb-3">+ Add New Flat</a>
    
    <!-- Flats List -->
    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4">
                <div class="hotel-card">
                    <img src="../uploads/<?php echo $row['image']; ?>" 
                         class="hotel-img"
                         onerror="this.src='../assets/img/default.jpg'">
                    
                    <h5 class="mt-2 mb-1"><?php echo $row['hotel_name']; ?></h5>
                    <p class="text-muted mb-1">📍 <?php echo $row['location']; ?></p>
                    <p class="mb-1"><strong>৳ <?php echo $row['price']; ?> / night</strong></p>
                    
                    <?php
                    $badge_class = 'pending-badge';
                    if($row['status'] == 'approved') $badge_class = 'approved-badge';
                    if($row['status'] == 'rejected') $badge_class = 'rejected-badge';
                    ?>
                    <span class="status-badge <?php echo $badge_class; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                    
                    <div class="mt-2">
                        <a href="edit_flat.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_flat.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this flat?')">Delete</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No flats added yet. <a href="upload_flat.php">Add your first flat</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
