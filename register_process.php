<?php
include "db_connect.php";

// Get and sanitize data
$name  = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role  = $_POST['role'];

// Email check
$check_sql = "SELECT id FROM users WHERE email='$email'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('Email already exists!'); window.history.back();</script>";
    exit();
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // 1. Insert user
    $user_sql = "INSERT INTO users (name, email, password, role, created_at) 
                 VALUES ('$name', '$email', '$pass', '$role', NOW())";
    
    if (!mysqli_query($conn, $user_sql)) {
        throw new Exception("User registration failed: " . mysqli_error($conn));
    }
    
    $user_id = mysqli_insert_id($conn);
    
    // 2. If owner, create hotel
    if ($role == 'owner') {
        $hotel_name = mysqli_real_escape_string($conn, $_POST['hotel_name'] ?? '');
        $hotel_location = mysqli_real_escape_string($conn, $_POST['hotel_location'] ?? '');
        
        if (empty($hotel_name) || empty($hotel_location)) {
            throw new Exception("Hotel name and location are required for owners");
        }
        
        // Fix: Use addslashes for locations like "Cox's Bazar"
        $hotel_location = addslashes($hotel_location);
        
        $hotel_sql = "INSERT INTO hotels (owner_id, hotel_name, location, status, created_at) 
                      VALUES ('$user_id', '$hotel_name', '$hotel_location', 'pending', NOW())";
        
        if (!mysqli_query($conn, $hotel_sql)) {
            throw new Exception("Hotel creation failed: " . mysqli_error($conn));
        }
        
        $hotel_id = mysqli_insert_id($conn);
        
        // Update owner_subscriptions with hotel_id
        mysqli_query($conn, "UPDATE owner_subscriptions SET hotel_id='$hotel_id' 
                            WHERE owner_id='$user_id' AND hotel_id IS NULL");
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Redirect to login
    echo "<script>
        alert('Registration successful! Please login.');
        window.location.href = 'login.php';
    </script>";
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    echo "<script>
        alert('Registration Error: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
}
?>