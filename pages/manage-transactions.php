<?php
require '../includes/functions.php'; // Include functions.php

if (!isset($_SESSION['username']) || !isAdmin()) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Fetch all transactions from the database
$stmt = $pdo->prepare("SELECT * FROM transactions ORDER BY created_at DESC");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Transactions</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="navbar">
    <a href="admin-dashboard.php"><i class="fas fa-cogs"></i> Admin Dashboard</a>
    <a href="manage-users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage-bank-logs.php"><i class="fas fa-building"></i> Manage Bank Logs</a>
    <a href="manage-cards.php"><i class="fas fa-credit-card"></i> Manage Cards</a>
    <a href="manage-transactions.php"><i class="fas fa-exchange-alt"></i> Manage Transactions</a>
    <a href="#" onclick="showModal('logoutModal')"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="container">
    <h1>Manage Transactions</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($transaction['amount'], 2)); ?> USD</td>
                        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['details']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modals -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to log out?</p>
        <button onclick="closeModal('logoutModal')">Cancel</button>
        <button onclick="window.location.href='../auth/logout.php'">Logout</button>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
