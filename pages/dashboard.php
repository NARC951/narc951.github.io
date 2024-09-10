<?php
require '../includes/db.php'; // Include the database connection
require '../includes/functions.php'; // Include necessary functions

// Ensure the user is authenticated
ensureAuthenticated();

// Fetch user details or other necessary data
$userId = $_SESSION['id']; // Get the logged-in user's ID

// Example of a database query using $pdo
try {
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $user['balance'];
} catch (PDOException $e) {
    echo "Error fetching user data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php include '../includes/user-navbar.php'; ?>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Your current balance is: <strong><?php echo number_format($balance, 2); ?> USD</strong></p>

    <!-- Add Funds Section -->
    <div class="section">
        <h2>Add Funds</h2>
        <p>To add funds, please go to the <a href="add-balance.php">Add Balance</a> page.</p>
    </div>

    <!-- Recent Transactions Section -->
    <div class="section">
        <h2>Recent Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['id']); ?></td>
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

    <!-- Quick Actions Section -->
    <div class="section">
        <h2>Quick Actions</h2>
        <a href="manage-cards.php" class="action-btn"><i class="fas fa-credit-card"></i> Purchase Cards</a>
        <a href="purchase-history.php" class="action-btn"><i class="fas fa-history"></i> View Purchase History</a>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
