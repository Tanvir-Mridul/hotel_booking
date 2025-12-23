<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$owner_id = $_SESSION['user_id'];

// Get flat data
$sql = "SELECT * FROM hotels WHERE id='$id' AND owner_id='$owner_id'";
$result = mysqli_query($conn, $sql);
$flat = mysqli_fetch_assoc($result);

// Update if form submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['hotel_name'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    
    $update_sql = "UPDATE hotels SET 
                   hotel_name='$name', 
                   location='$location', 
                   price='$price', 
                   description='$desc' 
                   WHERE id='$id'";
    mysqli_query($conn, $update_sql);
    
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Flat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; }
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover { background: #3498db; }
        .main {
            flex: 1;
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4>Owner Panel</h4>
    <a href="dashboard.php">Your Flats</a>
    <a href="upload_flat.php">Upload Flat</a>
    <a href="../logout.php" class="bg-danger">Logout</a>
</div>

<div class="main">
    <h3>Edit Flat</h3>
    
    <form method="POST" class="mt-4" style="max-width: 500px;">
        <div class="mb-3">
            <label>Flat Name</label>
            <input type="text" name="hotel_name" class="form-control" 
                   value="<?php echo $flat['hotel_name']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" 
                   value="<?php echo $flat['location']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Price (৳)</label>
            <input type="number" name="price" class="form-control" 
                   value="<?php echo $flat['price']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo $flat['description']; ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-success">Update</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>