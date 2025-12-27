<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update profile
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $update_sql = "UPDATE users SET name='$name', email='$email' WHERE id='$user_id'";
    if(mysqli_query($conn, $update_sql)) {
        $_SESSION['name'] = $name;
        header("Location: profile.php?msg=updated");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <!-- Include Header for Navbar -->
    <?php include "../header.php"; ?>    
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main {  padding: 20px; width: 100%; }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-icon {
            width: 80px;
            height: 80px;
            background: #3498db;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 15px;
        }
    </style>
</head>
<body>



<div class="main">
    <br>
    <br>
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-icon">
                <i class="fas fa-user"></i>
            </div>
            <h3>My Profile</h3>
            <p>Update your personal information</p>
        </div>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Profile updated successfully!
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" 
                       value="<?php echo $user['name']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo $user['email']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Account Type</label>
                <input type="text" class="form-control" 
                       value="<?php echo ucfirst($user['role']); ?>" disabled>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Member Since</label>
                <input type="text" class="form-control" 
                       value="<?php echo date('d M Y', strtotime($user['created_at'])); ?>" disabled>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>
    </div>
</div>

</body>
</html>