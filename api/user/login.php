<?php
require '../config/jwt.php';
require '../config/db.php';

// Validate user credentials
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Generate JWT payload
    $payload = JWTHandler::generatePayload($user['id'], $user['email']);
    
    // Encode the JWT
    $jwt = JWTHandler::encode($payload);
    
    echo json_encode(['token' => $jwt]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
?>
