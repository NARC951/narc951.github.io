<?php
require 'db.php'; // Include the database connection file

// Check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Check if the user is an admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

// Secure the session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
function ensureAuthenticated() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Redirect to login if not admin
function ensureAdmin() {
    if (!isAdmin()) {
        header("Location: ../auth/login.php");
        exit();
    }
}
?>
