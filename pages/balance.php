<?php
require '../includes/functions.php'; // Include necessary functions

// Ensure the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['id'];

// Fetch user's balances
try {
    $stmt = $pdo->prepare("SELECT currency, balance FROM user_balances WHERE user_id = ?");
    $stmt->execute([$userId]);
    $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='error-msg'>Error fetching balance data: " . $e->getMessage() . "</p>";
}

// Fetch real-time exchange rates from CoinGecko API
$apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,toncoin&vs_currencies=usd";
$exchangeRates = json_decode(file_get_contents($apiUrl), true); // Get the JSON response and decode it

// Set default exchange rates
$btcRate = $exchangeRates['bitcoin']['usd'] ?? 0;
$ethRate = $exchangeRates['ethereum']['usd'] ?? 0;
$tonRate = $exchangeRates['toncoin']['usd'] ?? 0;

// Generate unique deposit addresses (This part would be dynamic based on API used)
$btcDepositAddress = "btc_unique_address_for_user";  // Replace with actual logic
$ethDepositAddress = "eth_unique_address_for_user";  // Replace with actual logic
$tonDepositAddress = "ton_unique_address_for_user";  // Replace with actual logic

// Handle balance addition manually via deposit addresses (logic handled externally)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Balance</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/user-navbar.php'; ?>

<div class="container">
    <h1>Your Balance</h1>

    <!-- Display Current Balances -->
    <div class="balance-section">
        <h2>Current Balances</h2>
        <table>
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Balance</th>
                    <th>Value in USD</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($balances)):
                    foreach ($balances as $balance):
                        $valueInUSD = 0;
                        switch ($balance['currency']) {
                            case 'BTC':
                                $valueInUSD = $balance['balance'] * $btcRate;
                                break;
                            case 'ETH':
                                $valueInUSD = $balance['balance'] * $ethRate;
                                break;
                            case 'TON':
                                $valueInUSD = $balance['balance'] * $tonRate;
                                break;
                        }
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($balance['currency']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($balance['balance'], 8)); ?></td>
                            <td><?php echo htmlspecialchars('$' . number_format($valueInUSD, 2)); ?></td>
                        </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="3">No balances available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Display Deposit Addresses -->
    <div class="deposit-section">
        <h2>Deposit to Your Account</h2>
        <p>Use the addresses below to deposit cryptocurrency into your account:</p>
        <table>
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Deposit Address</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bitcoin (BTC)</td>
                    <td><?php echo $btcDepositAddress; ?></td>
                </tr>
                <tr>
                    <td>Ethereum (ETH)</td>
                    <td><?php echo $ethDepositAddress; ?></td>
                </tr>
                <tr>
                    <td>Toncoin (TON)</td>
                    <td><?php echo $tonDepositAddress; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Form to Add to Balance -->
    <div class="add-balance-section">
        <h2>Add to Your Balance</h2>
        <form method="POST" action="balance.php">
            <select name="currency" required>
                <option value="">Select Currency</option>
                <option value="BTC">Bitcoin (BTC)</option>
                <option value="ETH">Ethereum (ETH)</option>
                <option value="TON">Toncoin (TON)</option>
            </select>
            <input type="number" step="0.00000001" name="amount" placeholder="Amount" required>
            <button type="submit" name="add_balance" class="action-btn">
                <i class="fas fa-wallet"></i> Add Balance
            </button>
        </form>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
