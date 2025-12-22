<?php
include "../db_connect.php";


$owner_id = $_SESSION['owner_id'];



?>

<!DOCTYPE html>
<html>
<head>
<title>Owner Dashboard</title>

<link rel="stylesheet"
 href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
body{ background:#f5f6fa; }
.sidebar{
    width:220px;
    height:100vh;
    background:#2f3640;
    position:fixed;
}
.sidebar a{
    display:block;
    padding:12px 20px;
    color:#dcdde1;
    text-decoration:none;
}
.sidebar a:hover{
    background:#40739e;
    color:white;
}
.logout{ background:#c23616; }

.main{
    margin-left:230px;
    padding:20px;
}
.hotel-card img{
    height:150px;
    object-fit:cover;
}
</style>
</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h3 class="mb-4">Your Flats</h3>

    <div class="row">

<?php
$sql = "SELECT * FROM hotels WHERE owner_id='$owner_id'";
$res = mysqli_query($conn,$sql);

if(mysqli_num_rows($res)>0){
while($row=mysqli_fetch_assoc($res)){
?>

<div class="col-md-4 mb-4">
    <div class="card hotel-card">
        <img src="../uploads/<?php echo $row['image']; ?>" class="card-img-top">
        <div class="card-body">
            <h5><?php echo $row['hotel_name']; ?></h5>
            <p><?php echo $row['location']; ?></p>
            <p><strong>৳ <?php echo $row['price']; ?>/night</strong></p>

            <a href="edit_flat.php?id=<?php echo $row['id']; ?>"
               class="btn btn-sm btn-warning">Edit</a>

            <a href="delete_flat.php?id=<?php echo $row['id']; ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this hotel?')">
               Delete
            </a>
        </div>
    </div>
</div>

<?php }} else { ?>

<p class="text-muted">No flats added yet.</p>

<?php } ?>

    </div>
</div>

</body>
</html>
