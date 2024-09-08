<?php
// Add/remove cow from cart (simplified example)
require 'db.php';
require '../middleware/cors.php';
require '../middleware/auth.php';

enableCors();  // Enable CORS
$decodedUser = authenticate();  // Ensure user is authenticated

$action = $_POST['action']; // 'add' or 'remove'
$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];

if ($action === 'add') {
    $stmt = $pdo->prepare('INSERT INTO cart (user_id, product_id) VALUES (:user_id, :product_id)');
    $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
    echo json_encode(['message' => 'Product added to cart']);
} elseif ($action === 'remove') {
    $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id');
    $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
    echo json_encode(['message' => 'Product removed from cart']);
}
?>
