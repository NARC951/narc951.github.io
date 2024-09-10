<?php
$host = 'localhost'; // Database host
$db = 'cardshop'; // Database name
$user = 'card'; // Database user
$pass = 'card'; // Database password (leave empty if none)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
