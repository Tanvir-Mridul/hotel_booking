<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

// Function to escape single quotes
function clean_input($data) {
    return str_replace("'", "''", $data);
}

// Get and clean data
$owner_id = $_SESSION['user_id'];
$hotel_name = clean_input($_POST['hotel_name']);
$location = clean_input($_POST['location']);
$price = $_POST['price'];
$description = clean_input($_POST['description']);

// Upload image
$image_name = "default.jpg";
if(!empty($_FILES['image']['name'])) {
    $image_name = time() . "_" . str_replace("'", "", $_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_name);
}

// Insert query
$sql = "INSERT INTO hotels (owner_id, hotel_name, location, price, description, image, status) 
        VALUES ('$owner_id', '$hotel_name', '$location', '$price', '$description', '$image_name', 'pending')";

if(mysqli_query($conn, $sql)) {
    
    // Include notification helper
    include "../includes/notification_helper.php";
    
    // 🔔 Notify Admin - FIXED
    $admin_q = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
    
    if ($admin_q && mysqli_num_rows($admin_q) > 0) {
        $admin = mysqli_fetch_assoc($admin_q);
        $admin_id = $admin['id'];
        
        // Get owner name
        $owner_q = mysqli_query($conn, "SELECT name FROM users WHERE id='$owner_id'");
        $owner = mysqli_fetch_assoc($owner_q);
        $owner_name = $owner['name'] ?? "Owner ID: $owner_id";
        
        sendNotification($admin_id, 'admin',
            "🏨 New flat uploaded by $owner_name - \"$hotel_name\" at $location (৳$price)",
            "/hotel_booking/admin/hotels.php"
        );
    }
    
    // Also notify owner
    sendNotification($owner_id, 'owner',
        "📤 Your flat \"$hotel_name\" has been submitted for admin approval",
        "/hotel_booking/owner/dashboard.php"
    );
    
    header("Location: dashboard.php?msg=pending");
} else {
    echo "Database error!";
}
?>