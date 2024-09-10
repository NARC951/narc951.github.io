<?php
require '../includes/functions.php'; // Include necessary functions

// Start session if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = ''; // Initialize the error variable to prevent undefined warnings

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize email input
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $error = 'Please provide a valid email address.';
    } else {
        try {
            // Check if email exists in the database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate password reset token
                $resetToken = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :reset_token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
                $stmt->execute(['reset_token' => $resetToken, 'email' => $email]);

                // Send reset email (dummy implementation)
                $resetLink = "http://yourdomain.com/auth/password-reset.php?token=" . $resetToken;
                // Mail function to send email (use a real mail function in production)
                mail($email, "Password Reset", "Click the following link to reset your password: " . $resetLink);
                echo "<p class='success-msg'>A password reset link has been sent to your email.</p>";
            } else {
                $error = 'No account found with that email.';
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
    <title>Password Reset</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Hackerish Styles */
        body {
            font-family: 'Courier New', Courier, monospace; /* Code-like font */
            background-color: #0d0d0d; /* Dark background */
            color: #00ff00; /* Bright green text */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #111; /* Dark container background */
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px #00ff00; /* Green glow effect */
            max-width: 400px;
            width: 100%;
            text-align: center;
            border: 1px solid #00ff00; /* Green border */
        }

        .navbar {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .navbar a {
            margin-left: 15px;
            text-decoration: none;
            color: #00ff00; /* Bright green text */
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 10px;
            text-align: left;
            font-weight: bold;
            color: #a8ff60; /* Light green for better readability */
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #00ff00; /* Green border */
            border-radius: 4px;
            background-color: #0d0d0d; /* Dark input background */
            color: #00ff00; /* Green input text */
            margin-bottom: 15px;
        }

        .action-btn {
            padding: 10px;
            background-color: transparent; /* Transparent button background */
            color: #00ff00; /* Green text */
            border: 2px solid #00ff00; /* Green border */
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .action-btn:hover {
            background-color: #00ff00; /* Green background on hover */
            color: #0d0d0d; /* Dark text on hover */
        }

        .error-msg {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .success-msg {
            color: #a8ff60; /* Light green for success message */
            font-size: 14px;
            margin-top: 10px;
        }

        p a {
            color: #a8ff60; /* Light green for links */
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="../auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="../auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
</div>

<div class="container">
    <h2>Reset Your Password</h2>
    <form id="resetRequestForm" method="POST" action="password-reset-request.php">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit" class="action-btn"><i class="fas fa-envelope"></i> Send Reset Link</button>
        <?php if ($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
