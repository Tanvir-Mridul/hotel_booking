

<!DOCTYPE html>
<html>
<head>
<title>Upload Flat</title>

<link rel="stylesheet"
 href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
.main{ margin-left:230px; padding:30px; }
</style>
</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">
<h3>Add New Flat</h3>

<form action="insert_flat.php" method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>Hotel Name</label>
        <input type="text" name="hotel_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Location</label>
        <input type="text" name="location" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Price per Night</label>
        <input type="number" name="price" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <div class="form-group">
        <label>Image</label>
        <input type="file" name="image" class="form-control" required>
    </div>

    <button class="btn btn-primary">Upload</button>
</form>
</div>

</body>
</html>
