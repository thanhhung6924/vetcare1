<?php
// Thông số MoMo Sandbox
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMOBKUN20180810";
$accessKey = "WTM9RE9OTpCcByN6";
$secretKey = "str6nu9Dx949W9O49ez9A3gabmS66G";

$orderInfo = "Thanh toan don hang #" . $order_id;
$amount = (string)$order['total'];
$orderId = $order_id . "_" . time(); // ID duy nhất cho mỗi lần gọi
$redirectUrl = "http://localhost/order-success.php?order_id=" . $order_id;
$ipnUrl = "http://tên-miền-của-ông.com/api/momo_ipn.php"; // Quan trọng nhất ở đây
$requestId = time() . "";
$requestType = "captureWallet";
$extraData = "";

$rawHash = "accessKey=".$accessKey."&amount=".$amount."&extraData=".$extraData."&ipnUrl=".$ipnUrl."&orderId=".$orderId."&orderInfo=".$orderInfo."&partnerCode=".$partnerCode."&redirectUrl=".$redirectUrl."&requestId=".$requestId."&requestType=".$requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);

$data = array(
    'partnerCode' => $partnerCode, 'requestId' => $requestId,
    'amount' => $amount, 'orderId' => $orderId,
    'orderInfo' => $orderInfo, 'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl, 'extraData' => $extraData,
    'requestType' => $requestType, 'signature' => $signature, 'lang' => 'vi'
);

// Gọi API bằng CURL
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = json_decode(curl_exec($ch), true);

$payUrl = $result['payUrl'] ?? '#';
?>
