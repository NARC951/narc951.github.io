<?php
require '../includes/functions.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['username']) || !isAdmin()) {
    header("Location: ../auth/login.php"); // Redirect to login page if not an admin
    exit();
}

// Fetch total number of users
$stmtUsers = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
$stmtUsers->execute();
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total_users'];

// Fetch total deposits
$stmtDeposits = $pdo->prepare("SELECT SUM(amount) as total_deposits FROM transactions WHERE type = 'deposit' AND status = 'completed'");
$stmtDeposits->execute();
$totalDeposits = $stmtDeposits->fetch(PDO::FETCH_ASSOC)['total_deposits'] ?? 0.00;

// Fetch recent deposits
$stmtRecentDeposits = $pdo->prepare("SELECT * FROM transactions WHERE type = 'deposit' ORDER BY created_at DESC LIMIT 5");
$stmtRecentDeposits->execute();
$recentDeposits = $stmtRecentDeposits->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent transactions
$stmtRecentTransactions = $pdo->prepare("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 5");
$stmtRecentTransactions->execute();
$recentTransactions = $stmtRecentTransactions->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        }

        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            background-color: #111; /* Dark container background */
            border: 1px solid #00ff00; /* Green border */
            box-shadow: 0 0 10px #00ff00; /* Green glow effect */
            border-radius: 5px;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #1a1a1a; /* Darker section background */
            border: 1px solid #00ff00; /* Green border */
            border-radius: 4px;
        }

        h1, h2 {
            text-transform: uppercase;
            color: #00ff00; /* Bright green text */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #0d0d0d; /* Dark table background */
            border: 1px solid #00ff00; /* Green border */
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #00ff00; /* Green border */
            color: #00ff00; /* Bright green text */
        }

        th {
            background-color: #111; /* Slightly lighter background for headers */
        }

        tr:nth-child(even) {
            background-color: #1a1a1a; /* Alternating row color */
        }

        a {
            color: #00ff00; /* Bright green text for links */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include '../includes/admin-navbar.php'; ?>

<div class="container">
    <h1>Admin Dashboard</h1>

    <!-- Overview Section -->
    <div class="section">
        <h2>Overview</h2>
        <p>Total Users: <strong><?php echo $totalUsers; ?></strong></p>
        <p>Total Deposits: <strong><?php echo number_format($totalDeposits, 2); ?> USD</strong></p>
    </div>

    <!-- Recent Deposits Section -->
    <div class="section">
        <h2>Recent Deposits</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentDeposits)): ?>
                    <?php foreach ($recentDeposits as $deposit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($deposit['user_id']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($deposit['amount'], 2)); ?> USD</td>
                            <td><?php echo htmlspecialchars($deposit['status']); ?></td>
                            <td><?php echo htmlspecialchars($deposit['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No recent deposits found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Transactions Section -->
    <div class="section">
        <h2>Recent Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentTransactions)): ?>
                    <?php foreach ($recentTransactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($transaction['amount'], 2)); ?> USD</td>
                            <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No recent transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
