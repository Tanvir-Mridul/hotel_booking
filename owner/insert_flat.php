<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

// Function to escape single quotes
function clean_input($data) {
    // Replace single quote with double single quotes for SQL
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
    // Clean image filename too
    $image_name = time() . "_" . str_replace("'", "", $_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_name);
}

// Insert query
$sql = "INSERT INTO hotels (owner_id, hotel_name, location, price, description, image, status) 
        VALUES ('$owner_id', '$hotel_name', '$location', '$price', '$description', '$image_name', 'pending')";

// Debug: Show the SQL
// echo "SQL: " . htmlspecialchars($sql) . "<br>";

if(mysqli_query($conn, $sql)) {
    header("Location: dashboard.php?msg=pending");
} else {
    // If still error, try prepared statement
    echo "<h3>Error Uploading Flat</h3>";
    echo "Error: " . mysqli_error($conn) . "<br><br>";
    
    // Try alternative method
    $stmt = $conn->prepare("INSERT INTO hotels (owner_id, hotel_name, location, price, description, image, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issdss", $owner_id, $hotel_name, $location, $price, $description, $image_name);
    
    if($stmt->execute()) {
        header("Location: dashboard.php?msg=pending");
    } else {
        echo "Prepared statement error: " . $stmt->error . "<br>";
        echo "<a href='upload_flat.php' class='btn btn-primary'>Try Again</a>";
    }
}
?>