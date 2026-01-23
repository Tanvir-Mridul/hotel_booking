
<?php
session_start();
include "../db_connect.php";

/*
|--------------------------------------------------------------------------
| SSLCommerz Cancel URL
|--------------------------------------------------------------------------
|
*/

if (!isset($_POST['tran_id'])) {
    die("Invalid access");
}

$tran_id = $_POST['tran_id'];

// Subscription update → cancelled
$sql = "UPDATE owner_subscriptions 
        SET payment_status = 'cancelled', status = 'cancelled' 
        WHERE tran_id = '$tran_id'";

mysqli_query($conn, $sql);

// Optional: Log cancel info
// file_put_contents("ssl_cancel.log", json_encode($_POST), FILE_APPEND);

// Message for UI
$_SESSION['msg'] = "❌ Payment cancelled. Subscription not activated.";

// Redirect owner dashboard / subscription page
header("Location: ../owner/subscription.php");
exit();
