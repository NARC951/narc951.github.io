<?php
require '../includes/functions.php'; // Include functions.php

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in
    exit();
}

$userId = $_SESSION['id']; // Get the logged-in user's ID

// Dummy cryptocurrency address (replace with actual implementation)
$cryptoAddress = '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa';

// Fetch user's balance from the database
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'];

// Generate QR code for payment
$qrCodeUrl = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=bitcoin:' . $cryptoAddress . '?amount=0.001&choe=UTF-8';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Balance</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="navbar">
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="bank-logs.php"><i class="fas fa-building"></i> Bank Logs</a>
    <a href="card.php"><i class="fas fa-credit-card"></i> Card</a>
    <a href="account.php"><i class="fas fa-key"></i> Account</a>
    <a href="spamming.php"><i class="fas fa-file-alt"></i> Spamming</a>
    <a href="balance.php"><i class="fas fa-dollar-sign"></i> Balance: <?php echo number_format($balance, 2); ?> USD</a>
    <a href="#" onclick="showModal('logoutModal')"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="container">
    <h1>Add Balance</h1>
    <p>Your current balance is: <strong><?php echo number_format($balance, 2); ?> USD</strong></p>
    <p>To add funds, please send cryptocurrency to the address below:</p>
    <p><strong>Address: <?php echo $cryptoAddress; ?></strong></p>
    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
    <p>Scan the QR code to make a payment of <strong>0.001 BTC</strong> or use the address above.</p>
    <p>Your balance will be updated once the transaction is confirmed.</p>

    <!-- Simulate Deposit Confirmation -->
    <form method="POST" action="add-balance.php" class="form-section">
        <input type="hidden" name="crypto_address" value="<?php echo $cryptoAddress; ?>">
        <input type="number" step="0.01" name="amount" placeholder="Enter deposit amount" required>
        <button type="submit" name="confirm_deposit" class="action-btn"><i class="fas fa-check-circle"></i> Confirm Deposit</button>
    </form>
</div>

<!-- Modals -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to log out?</p>
        <button onclick="closeModal('logoutModal')">Cancel</button>
        <button onclick="window.location.href='../auth/logout.php'">Logout</button>
    </div>
</div>

<script>
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>
</body>
</html>
