<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all users
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; margin: 0; background: #f5f5f5; }
        .main { margin-left: 220px; padding: 20px; width: 100%; }
        .table-box { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge-user { background: #3498db; color: white; }
        .badge-owner { background: #2ecc71; color: white; }
        .badge-admin { background: #e74c3c; color: white; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3>Manage Users</h3>
    
    <div class="table-box">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <?php 
                        $badge_class = 'badge-user';
                        if($row['role'] == 'owner') $badge_class = 'badge-owner';
                        if($row['role'] == 'admin') $badge_class = 'badge-admin';
                        ?>
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $row['role']; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this user?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No users found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>