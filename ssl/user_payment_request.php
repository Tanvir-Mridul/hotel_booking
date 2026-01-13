<?php
session_start();
include "../db_connect.php";

if (!isset($_POST['booking_id'], $_POST['amount'])) {
    die("Invalid Request");
}

$booking_id = (int)$_POST['booking_id'];
$amount = $_POST['amount'];
$user_id = $_SESSION['user_id'];

/* SSLCommerz Credentials (Apnar existing same) */
$store_id = "hotel6952af0ce15dc";
$store_passwd = "hotel6952af0ce15dc@ssl";

/* Transaction info */
$tran_id = "USERPAY_" . uniqid();

$post_data = array();
$post_data['store_id'] = $store_id;
$post_data['store_passwd'] = $store_passwd;
$post_data['total_amount'] = $amount;
$post_data['currency'] = "BDT";
$post_data['tran_id'] = $tran_id;
$post_data['success_url'] = "http://localhost/hotel_booking/ssl/user_success.php";
$post_data['fail_url'] = "http://localhost/hotel_booking/ssl/fail.php";
$post_data['cancel_url'] = "http://localhost/hotel_booking/ssl/cancel.php";
$post_data['ipn_url'] = "http://localhost/hotel_booking/ssl/ipn.php";

/* Customer info */
$post_data['cus_name'] = $_SESSION['name'] ?? "User";
$post_data['cus_email'] = "user@test.com";
$post_data['cus_phone'] = "01700000000";
$post_data['cus_add1'] = "Dhaka";
$post_data['cus_city'] = "Dhaka";
$post_data['cus_country'] = "Bangladesh";
$post_data['shipping_method'] = "NO";

/* Product info */
$post_data['product_name'] = "Hotel Booking Payment";
$post_data['product_category'] = "Hotel";
$post_data['product_profile'] = "general";

$post_data['value_a'] = $booking_id; // booking_id
$post_data['value_b'] = $user_id; // user_id

/* CURL (Same as subscription) */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sandbox.sslcommerz.com/gwprocess/v4/api.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if ($response === false) {
    die("CURL ERROR: " . curl_error($ch));
}

curl_close($ch);

/* Debug */
$result = json_decode($response, true);

if (!is_array($result)) {
    echo "<h4>JSON Data parsing error!</h4>";
    echo "<pre>";
    print_r($response);
    exit;
}

if (isset($result['GatewayPageURL']) && $result['GatewayPageURL'] != "") {
    header("Location: " . $result['GatewayPageURL']);
    exit;
} else {
    echo "Payment initialization failed";
    echo "<pre>";
    print_r($result);
}
?>