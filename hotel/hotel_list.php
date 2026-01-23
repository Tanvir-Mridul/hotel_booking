<?php
session_start();
include "../db_connect.php";
include "../header.php";

/* ====== Search Values ====== */
$location = "";
$min_price = "";
$max_price = "";

$where = "WHERE h.status = 'approved' AND r.active = 1";


/*  LOCATION SEARCH */
if (!empty($_GET['location'])) {
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $where .= " AND (h.location LIKE '%$location%' 
                OR h.hotel_name LIKE '%$location%')";
}

/*  MIN PRICE */
if (!empty($_GET['min_price'])) {
    $min_price = (int)$_GET['min_price'];
    $where .= " AND (
        CASE 
            WHEN r.discount_price IS NOT NULL 
                 AND r.discount_price > 0 
            THEN r.discount_price 
            ELSE r.price_per_night 
        END
    ) >= $min_price";
}

/* MAX PRICE */
if (!empty($_GET['max_price'])) {
    $max_price = (int)$_GET['max_price'];
    $where .= " AND (
        CASE 
            WHEN r.discount_price IS NOT NULL 
                 AND r.discount_price > 0 
            THEN r.discount_price 
            ELSE r.price_per_night 
        END
    ) <= $max_price";
}



$sql = "SELECT 
    h.*,
    MIN(
        CASE 
            WHEN r.discount_price IS NOT NULL 
                 AND r.discount_price > 0 
                 AND r.discount_price < r.price_per_night
            THEN r.discount_price
            ELSE r.price_per_night
        END
    ) AS lowest_price
FROM hotels h
LEFT JOIN rooms r ON h.id = r.hotel_id
$where
GROUP BY h.id
ORDER BY h.id ASC
";


$result = mysqli_query($conn, $sql);
?>


<link rel="stylesheet" href="../style.css">
<div class="container mt-5">
    <h2 class="text-center mb-4">Search Hotels</h2>

    <!--  Search Box -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <input type="text" name="location" value="<?php echo $location; ?>"
                   class="form-control" placeholder="Enter location (e.g. Cox's Bazar)">
        </div>

        <div class="col-md-3">
            <input type="number" name="min_price" value="<?php echo $min_price; ?>"
                   class="form-control" placeholder="Min Price">
        </div>

        <div class="col-md-3">
            <input type="number" name="max_price" value="<?php echo $max_price; ?>"
                   class="form-control" placeholder="Max Price">
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" name="search" class="btn btn-primary">
                Search
            </button>
        </div>
    </form>

    <!--  Hotel Cards -->
    <div class="row">

    

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <img src="../uploads/<?php echo $row['image']; ?>"
                             class="card-img-top"
                             style="height:220px; object-fit:cover;">

                        <div class="card-body">
                            <h5><?php echo $row['hotel_name']; ?></h5>

                            <p class="text-muted mb-1">
                                📍 <?php echo $row['location']; ?>
                            </p>

                            <?php if (!empty($row['lowest_price'])): ?>
    <h6 class="text-success">
         ৳ <?php echo number_format($row['lowest_price'], ); ?> / night
    </h6>
<?php else: ?>
    <h6 class="text-muted">
        Price not available
    </h6>
<?php endif; ?>


                            <p>
                                <?php echo substr($row['description'], 0, 70); ?>...
                            </p>
                        </div>

                       <!-- Hotel Card er bitor -->
<div class="card-footer bg-white border-0">
    <a href="hotel_details.php?id=<?php echo $row['id']; ?>"
       class="btn btn-outline-primary w-100">
        View Hotel & Rooms
    </a>
</div>

                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-danger">No hotels found</p>
        <?php endif; ?>

    </div>
</div>

<?php include "../footer.php"; ?>
