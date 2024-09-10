<?php
require '../includes/functions.php'; // Include necessary functions

// Start session if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = ''; // Variable to store error messages
$success = false; // Flag to indicate success

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = htmlspecialchars($_POST['token']);
    $newPassword = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

    // Validate token and passwords
    if ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if token is valid
            $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :reset_token AND reset_token_expiry > NOW()");
            $stmt->execute(['reset_token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                $stmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);
                $success = true;
            } else {
                $error = 'Invalid or expired token.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred while processing your request. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="navbar">
    <a href="../auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="../auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
</div>

<div class="container">
    <h2>Set a New Password</h2>
    <?php if ($success): ?>
        <p class="success-msg">Your password has been reset successfully. <a href="login.php">Login here</a>.</p>
    <?php else: ?>
        <form id="resetForm" method="POST" action="password-reset.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            <label for="password">New Password:</label>
            <input type="password" name="password" required>
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            <button type="submit" class="action-btn"><i class="fas fa-key"></i> Reset Password</button>
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
