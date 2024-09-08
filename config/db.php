<?php
$host = 'localhost';
$dbname = 'kapkatet_dairy';
$username = 'root'; // Set your username
$password = '';     // Set your password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}
?>
