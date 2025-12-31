<?php
include "../db_connect.php";

// 1️⃣ Find expired subscriptions
$expired_q = mysqli_query($conn, "SELECT id, owner_id
    FROM owner_subscriptions
    WHERE status='approved'
    AND end_date < CURDATE()
");

while ($sub = mysqli_fetch_assoc($expired_q)) {

    $sub_id = $sub['id'];
    $owner_id = $sub['owner_id'];

    // 2️⃣ Mark subscription expired
    $expired_subs = mysqli_query($conn, "SELECT owner_id FROM owner_subscriptions 
    WHERE status='approved' AND end_date < CURDATE()
");

while($sub = mysqli_fetch_assoc($expired_subs)){
    $owner_id = $sub['owner_id'];
    mysqli_query($conn, "UPDATE hotels SET status='off' WHERE owner_id='$owner_id'
    ");
}

// তারপর owner_subscriptions expired update
mysqli_query($conn, "UPDATE owner_subscriptions 
    SET status='expired'
    WHERE status='approved' AND end_date < CURDATE()
");


    // 3️⃣ Disable owner's hotels/flats
    mysqli_query($conn, "UPDATE hotels
    SET status='off'
    WHERE owner_id='$owner_id'
    AND status='approved'
    ");

    // 4️⃣ Optional: send notification
    mysqli_query($conn, "INSERT INTO notifications (receiver_id, receiver_role, message)
        VALUES (
            '$owner_id',
            'owner',
            '⚠️ Your subscription has expired. Hotels are now disabled.'
        )
    ");
}

echo "Subscription expiry check completed";
