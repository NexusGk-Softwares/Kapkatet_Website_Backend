<?php
require '../vendor/autoload.php';

use Models\User;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up database connection
require '../config/db.php';

// Instantiate User model
$userModel = new User($pdo);

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'], $data['email'], $data['password'])) {
    $name = htmlspecialchars(strip_tags($data['name']));
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];

    if ($email) {
        $registered = $userModel->register($name, $email, $password);
        if ($registered) {
            echo json_encode(['message' => 'User registered successfully']);
        } else {
            echo json_encode(['error' => 'Registration failed']);
        }
    } else {
        echo json_encode(['error' => 'Invalid email address']);
    }
} else {
    echo json_encode(['error' => 'Missing required fields']);
}
?>
