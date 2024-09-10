<?php
require '../includes/functions.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

// Generate a CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);

    // Validate form inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword]);

            // Redirect to login page
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
      <style>
        /* Hackerish Styles */
        body {
            font-family: 'Courier New', Courier, monospace;
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
            background-color: #111; /* Very dark background */
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

        .auth-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .input-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #a8ff60; /* Light green for better readability */
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #00ff00; /* Green border */
            border-radius: 4px;
            background-color: #0d0d0d; /* Dark input background */
            color: #00ff00; /* Green input text */
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

        p {
            margin-top: 20px;
            color: #00ff00; /* Bright green text */
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
<div class="navbar">
    <a href="../auth/login.php">Login</a>
    <a href="../auth/register.php">Register</a>
</div>
<div class="container">
    <h2>Register</h2>
    <form method="POST" action="register.php" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="input-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="input-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit" class="action-btn">Register</button>
        <p class="error-msg"><?php echo $error; ?></p>
    </form>
</div>
</body>
</html>
