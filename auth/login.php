<?php
require '../includes/functions.php';

$error = '';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}

// Generate a CSRF token if it does not exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die('Invalid CSRF token');
    }

    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Both fields are required.';
    } else {
        // Fetch user data from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables and redirect to dashboard
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
<body>
<div class="navbar">
    <a href="../auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
</div>
<div class="container">
    <h1>Login</h1>
    <form method="POST" action="login.php" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="input-group">
            <label for="username"><i class="fas fa-user"></i> Username</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
        </div>
        <div class="input-group">
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="action-btn"><i class="fas fa-sign-in-alt"></i> Login</button>
        <?php if (!empty($error)): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
    <p>Forgot your password? <a href="password-reset-request.php">Reset it here</a></p>
    <p>New user? <a href="register.php">Sign up here</a></p>
</div>
</body>
</html>

