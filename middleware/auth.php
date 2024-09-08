<?php
require '../config/jwt.php';

function authenticate() {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $jwt = $matches[1];
    $decoded = JWTHandler::validate($jwt);

    if (!$decoded) {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }

    return $decoded; // Return the decoded user data
}
?>
