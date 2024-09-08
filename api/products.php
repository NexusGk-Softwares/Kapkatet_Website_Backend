<?php
require '../middleware/cors.php';
enableCors();  // Enable CORS for this endpoint

// Fetch all products (cows)
require '../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>
