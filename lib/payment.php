<?php
class Daraja {
    private $consumer_key;
    private $consumer_secret;
    private $base_url;

    public function __construct() {
        $this->consumer_key = getenv('DARAJA_CONSUMER_KEY');
        $this->consumer_secret = getenv('DARAJA_CONSUMER_SECRET');
        $this->base_url = 'https://sandbox.safaricom.co.ke';
    }

    public function authenticate() {
        $url = $this->base_url . '/oauth/v1/generate?grant_type=client_credentials';
        $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if (!$response) {
            die('Connection Error: ' . curl_error($curl));
        }

        curl_close($curl);
        return json_decode($response)->access_token;
    }

    // You can add more functions here for initiating payments
}
