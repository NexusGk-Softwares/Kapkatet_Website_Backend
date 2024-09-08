<?php
// Fetch product by ID
require 'db.php';

$id = $_GET['id']; // Get product ID from URL

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($product);
?>
