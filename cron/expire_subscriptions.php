<?php
include "../db_connect.php";
include "../includes/notification_helper.php";

//  Find expired subscriptions
$expired_subs = mysqli_query($conn, "SELECT id, owner_id 
    FROM owner_subscriptions 
    WHERE status='approved' 
    AND end_date < CURDATE()
");

while ($sub = mysqli_fetch_assoc($expired_subs)) {
    $sub_id = $sub['id'];
    $owner_id = $sub['owner_id'];

    //  Mark subscription as expired
    mysqli_query($conn, "UPDATE owner_subscriptions 
        SET status='expired' 
        WHERE id='$sub_id'");

    //  Disable owner's hotels
    mysqli_query($conn, "UPDATE hotels 
        SET status='off' 
        WHERE owner_id='$owner_id' 
        AND status='approved'");

    //  Send notification
    sendNotification($owner_id, 'owner',
        "⚠️ Your subscription has expired. Hotels are now disabled. Please renew.",
        "/hotel_booking/owner/subscription.php"
    );
}

echo "Subscription expiry check completed";
?>