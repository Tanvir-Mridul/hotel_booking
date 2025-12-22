<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include "../header.php";
?>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Manage Hotels</h4>
                <a href="hotels.php" class="btn btn-primary">View Hotels</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Logout</h4>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>


