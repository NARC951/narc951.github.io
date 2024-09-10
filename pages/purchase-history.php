<?php
require '../includes/functions.php'; // Include necessary functions

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch purchase history from the database
$userId = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT ph.id, c.card_number, c.card_number_masked, c.expiration, c.cc_type, c.price, ph.created_at
                       FROM purchase_history ph
                       JOIN cards c ON ph.card_id = c.id
                       WHERE ph.user_id = :user_id ORDER BY ph.created_at DESC");
$stmt->execute(['user_id' => $userId]);
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php include '../includes/user-navbar.php'; ?>

<div class="container">
    <h1>Purchase History</h1>
    <table>
        <thead>
            <tr>
                <th>Card Number</th>
                <th>Expiration Date</th>
                <th>CC Type</th>
                <th>Price</th>
                <th>Purchase Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($purchases)): ?>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($purchase['card_number']); ?></td>
                        <td><?php echo htmlspecialchars($purchase['expiration']); ?></td>
                        <td><?php echo htmlspecialchars($purchase['cc_type']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($purchase['price'], 2)); ?> USD</td>
                        <td><?php echo htmlspecialchars($purchase['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No purchase history found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
