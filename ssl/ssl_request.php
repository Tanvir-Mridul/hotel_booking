<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_POST['package_id'], $_POST['amount'])) {
    die("Invalid data");
}

$package_id = (int) $_POST['package_id'];
$amount = (float) $_POST['amount'];

// Get package details
$pkg_q = mysqli_query($conn, "SELECT * FROM subscriptions WHERE id='$package_id'");
if (mysqli_num_rows($pkg_q) == 0) {
    die("Invalid package");
}
$package = mysqli_fetch_assoc($pkg_q);

// SSLCommerz Credentials (Sandbox)
$store_id = "your_store_id"; // Replace with your sandbox store id
$store_passwd = "your_store_password"; // Replace with your sandbox password
$curl_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

// Transaction ID
$tran_id = "SUBS_".time()."_".$owner_id;

// Success, Fail, Cancel URLs
$success_url = "http://localhost/hotel_booking/ssl/subscription_success.php";
$fail_url = "http://localhost/hotel_booking/ssl/subscription_fail.php";
$cancel_url = "http://localhost/hotel_booking/ssl/subscription_fail.php";

// POST Data for SSLCommerz
$post_data = array();
$post_data['store_id'] = $store_id;
$post_data['store_passwd'] = $store_passwd;
$post_data['total_amount'] = $amount;
$post_data['currency'] = "BDT";
$post_data['tran_id'] = $tran_id;
$post_data['success_url'] = $success_url;
$post_data['fail_url'] = $fail_url;
$post_data['cancel_url'] = $cancel_url;
$post_data['emi_option'] = 0;

// Customer info
$post_data['cus_name'] = $_SESSION['name'];
$post_data['cus_email'] = $_SESSION['email'] ?? "owner@example.com";
$post_data['cus_add1'] = "";
$post_data['cus_city'] = "";
$post_data['cus_postcode'] = "";
$post_data['cus_country'] = "Bangladesh";
$post_data['cus_phone'] = $_SESSION['phone'] ?? "017XXXXXXXX";

// Shipment info (optional)
$post_data['ship_name'] = "N/A";
$post_data['ship_add1'] = "N/A";
$post_data['ship_city'] = "Dhaka";
$post_data['ship_country'] = "Bangladesh";
$post_data['ship_phone'] = $_SESSION['phone'] ?? "017XXXXXXXX";
$post_data['ship_postcode'] = "1000";

// Optional parameters
$post_data['value_a'] = $owner_id;
$post_data['value_b'] = $package_id;

// Initiate cURL
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $curl_url);
curl_setopt($handle, CURLOPT_TIMEOUT, 30);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($handle, CURLOPT_POST, 1);
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

// Execute
$content = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code == 200 && !(curl_errno($handle))) {
    $response = json_decode($content, true);
    if (isset($response['GatewayPageURL']) && $response['GatewayPageURL'] != "") {
        header("Location: " . $response['GatewayPageURL']);
        exit();
    } else {
        die("JSON Data parsing error!");
    }
} else {
    die("cURL Error: " . curl_errno($handle));
}

curl_close($handle);
