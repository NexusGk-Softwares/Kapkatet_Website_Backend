<?php
// CORS middleware to allow requests from any origin
function enableCors() {
    // Allow from any origin
    header("Access-Control-Allow-Origin: *");

    // Allow the following request methods (GET, POST, PUT, DELETE)
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    // Allow the following headers
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Handle preflight requests (OPTIONS method)
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // No content response (204), because OPTIONS requests are not actual data requests
        http_response_code(204);
        exit;
    }
}
?>
