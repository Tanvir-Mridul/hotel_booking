<?php
session_start();
include "../db_connect.php";


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}
$owner_id = $_SESSION['user_id'];

/* ===== COUNT OWNER FLATS ===== */
$count_q = mysqli_query($conn, "SELECT COUNT(*) AS total 
    FROM hotels 
    WHERE owner_id='$owner_id'
");
$total_flats = mysqli_fetch_assoc($count_q)['total'];

/* ===== PREMIUM CHECK ===== */
$is_premium = false;

$sub_q = mysqli_query($conn, "SELECT id FROM owner_subscriptions
    WHERE owner_id='$owner_id'
    AND status='approved'
    AND end_date >= CURDATE()
    LIMIT 1
");

if ($sub_q && mysqli_num_rows($sub_q) > 0) {
    $is_premium = true;
}

$check = mysqli_query($conn,
    "SELECT * FROM owner_subscriptions 
     WHERE owner_id='$owner_id' AND status='approved' 
     AND end_date >= CURDATE()"
);


/* ===== FLAT LIMIT CHECK ===== */
if (!$is_premium && $total_flats >= 1) {
    echo "
    <div style='padding:20px; margin:40px auto; max-width:600px;
         background:#fff3cd; border-left:5px solid #ffc107'>
        <h4>🚫 Flat Limit Reached</h4>
        <p>Free owners can upload only <b>1 flat</b>.</p>
        <a href='subscription.php' class='btn btn-warning'>
            Upgrade to Premium
        </a>
    </div>
    ";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Flat</title>

    <!-- ✅ Bootstrap 4 CSS -->
    
     <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>

    <style>
        body { 
            display: flex; 
            margin: 0; 
            font-family: 'Segoe UI', Arial; 
            background: #f8f9fa;
        }

        .main-content {
            margin-top: 40px;
            padding: 30px;
            width: 100%;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin: 0 auto;
        }

        .page-title {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .submit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
        }

        .submit-btn:hover {
            background: #2980b9;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>


 <?php include "../header.php"; ?>
<div class="main-content">

    <!-- Header -->
    <div class="page-header">
        <h3 class="mb-1">
            <i class="fas fa-cloud-upload-alt"></i> Upload New Flat
        </h3>
        <p class="text-muted mb-0">
            Add your flat details below. It will be reviewed by admin.
        </p>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="insert_flat.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Flat Name *</label>
                <input type="text" name="hotel_name" class="form-control"
                       placeholder="Enter flat/hotel name" required>
            </div>

            <div class="form-group">
                <label>Location *</label>
                <input type="text" name="location" class="form-control"
                       placeholder="e.g., Cox's Bazar" required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Price per Night (৳) *</label>
                    <input type="number" name="price" class="form-control"
                           placeholder="e.g., 2500" min="100" required>
                </div>

                <div class="form-group col-md-6">
                    <label>Number of Rooms</label>
                    <select name="rooms" class="form-control">
                        <option value="1">1 Room</option>
                        <option value="2">2 Rooms</option>
                        <option value="3">3 Rooms</option>
                        <option value="4">4+ Rooms</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Describe your flat, amenities, facilities..."></textarea>
            </div>

            <div class="form-group">
                <label>Upload Image *</label>
                <input type="file" name="image" class="form-control-file" required>
                <small class="text-muted">Max size: 2MB. JPG, PNG, JPEG</small>
            </div>
             
            <button type="submit" class="submit-btn">
                <i class="fas fa-upload"></i> Upload Flat
            </button>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                <strong> Note:</strong> Your flat will be reviewed by admin before appearing on the website.
            </div>

        </form>
    </div>
</div>

<!-- ✅ Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
