<?php

// Function to get M-Pesa token
function getMpesaToken() {
    $consumer_key = 'pfMt3jO2VmGRn55B857LO5p3b7Vh3WFbaW2z8RGP6khDNGCk';
    $consumer_secret = 'y4wusSjMnLRhd6Wj625HXEiJ1fThfyDgPfJiZDlaQ9o9ixyA2xVF4OrRGZgJhHtF';
    
    $api_URL = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    
    // Combine consumer key and secret, and encode them to base64
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    
    // Set the headers for the request
    $headers = [
        "Authorization: Basic " . $credentials
    ];
    
    // Make the request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decode the response
    $result = json_decode($response, true);
    
    // Return the access token
    return $result['access_token'];
}

// Function to handle STK Push request
function makeSTKPush($phone, $amount) {
    // Encode business shortcode, passkey, and timestamp to base64
    $business_shortcode = '174379';
    $online_passkey = '<online_passkey>';
    $timestamp = date('YmdHis'); // Current timestamp
    $password = base64_encode($business_shortcode . $online_passkey . $timestamp);
    
    // Get the access token
    $access_token = getMpesaToken();
    
    // Set the API URL
    $api_url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
    
    // Set the headers
    $headers = [
        "Authorization: Bearer " . $access_token,
        "Content-Type: application/json"
    ];
    
    // Prepare the request body
    $request = [
        "BusinessShortCode" => $business_shortcode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $business_shortcode,
        "PhoneNumber" => $phone,
        "CallBackURL" => "https://mydomain.com/callback",
        "AccountReference" => "KapKatet Dairy Farm",
        "TransactionDesc" => "Test"
    ];
    
    // Make the request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check if the response is successful
    if ($http_code > 299) {
        return [
            "success" => false,
            "message" => "Sorry, something went wrong please try again later."
        ];
    }
    
    // Decode the response
    $response_data = json_decode($response, true);
    
    // You can store the CheckoutRequestID in your database for later use
    // $CheckoutRequestID = $response_data['CheckoutRequestID'];

    // Return response data
    return [
        "success" => true,
        "data" => $response_data
    ];
}

// Example usage:
$phone = "254712345678";
$amount = "100";
$response = makeSTKPush($phone, $amount);

// Output the response
header('Content-Type: application/json');
echo json_encode($response);
?>
