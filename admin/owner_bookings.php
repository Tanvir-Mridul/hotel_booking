<?php
include "../db_connect.php";

$sql = "
SELECT 
    o.id AS owner_id,
    o.name AS owner_name,
    COUNT(b.id) AS total_bookings
FROM users o
LEFT JOIN bookings b ON o.id = b.owner_id
WHERE o.role = 'owner'
GROUP BY o.id
ORDER BY total_bookings DESC
";

$result = mysqli_query($conn, $sql);
