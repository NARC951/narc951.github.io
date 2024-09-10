<?php
require 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    header("Location: pages/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CCShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container">
        <h1>Welcome to CCShop</h1>
        <p>Your one-stop platform for buying 100% SNIFFED CC DATA, LOGS, and templates.</p>
    </div>
</header>

<main class="main-content">
    <!-- Features Section -->
    <section class="features container">
        <div class="feature">
            <i class="fas fa-credit-card feature-icon"></i>
            <h2>Buy Cards</h2>
            <p>Purchase cards and logs securely and efficiently, with all cryptocurrency.</p>
        </div>
        <div class="feature">
            <i class="fas fa-chart-line feature-icon"></i>
            <h2>Refunds</h2>
            <p>5 min check time, Auto refund, Live CC checker.</p>
        </div>
        <div class="feature">
            <i class="fas fa-user-shield feature-icon"></i>
            <h2>Fresh Account</h2>
            <p>Never re-sold, Sniffed daily, request accepted, sellers wanted, price $5 all items.</p>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta container">
        <a href="auth/register.php" class="btn primary-btn"><i class="fas fa-user-plus"></i> Create an Account</a>
        <a href="auth/login.php" class="btn secondary-btn"><i class="fas fa-sign-in-alt"></i> Login to Your Account</a>
    </section>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2024 CCShop. All Rights Reserved.</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
