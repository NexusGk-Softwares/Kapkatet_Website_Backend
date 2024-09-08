<?php
// Order placement and Daraja API payment
require 'db.php';

// Process order here (insert into 'orders', 'order_item' tables)

// Call Daraja API for payment
$mpesa_endpoint = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $mpesa_endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($payment_data)
));

$response = curl_exec($curl);
curl_close($curl);

// Update payment status based on Daraja response
echo json_encode(['message' => 'Order placed and payment processed']);
?>
