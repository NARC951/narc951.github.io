<?php
require '../includes/db.php'; // Include the database connection
require '../includes/functions.php'; // Include necessary functions

// Ensure the user is authenticated
ensureAuthenticated();

// Get the logged-in user's ID
$userId = $_SESSION['id'];

// Function to sanitize inputs
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Handle adding a new card (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card']) && isAdmin()) {
    $cardNumber = sanitizeInput($_POST['card_number']);
    $expiration = sanitizeInput($_POST['expiration']);
    $ccType = sanitizeInput($_POST['cc_type']);
    $bin = sanitizeInput($_POST['bin']);
    $city = sanitizeInput($_POST['city']);
    $state = sanitizeInput($_POST['state']);
    $zip = sanitizeInput($_POST['zip']);
    $country = sanitizeInput($_POST['country']);
    $price = floatval($_POST['price']);

    // Validate inputs
    if (!ctype_digit($bin) || strlen($bin) !== 6) {
        echo "<p class='error-msg'>Invalid BIN number. Must be 6 digits.</p>";
    } elseif (!preg_match('/^\d{2}\/\d{4}$/', $expiration)) {
        echo "<p class='error-msg'>Invalid expiration date format. Use MM/YYYY.</p>";
    } elseif (!is_numeric($price) || $price <= 0) {
        echo "<p class='error-msg'>Invalid price.</p>";
    } else {
        // Mask the card number (show only first 6 digits)
        $cardNumberMasked = substr($cardNumber, 0, 6) . str_repeat('X', strlen($cardNumber) - 6);

        try {
            // Insert new card into the database
            $stmt = $pdo->prepare("INSERT INTO cards (card_number, card_number_masked, expiration, cc_type, bin, city, state, zip, country, price) VALUES (:card_number, :card_number_masked, :expiration, :cc_type, :bin, :city, :state, :zip, :country, :price)");
            $stmt->execute([
                'card_number' => $cardNumber,
                'card_number_masked' => $cardNumberMasked,
                'expiration' => $expiration,
                'cc_type' => $ccType,
                'bin' => $bin,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'country' => $country,
                'price' => $price
            ]);
            echo "<p class='success-msg'>Card added successfully!</p>";
        } catch (PDOException $e) {
            error_log($e->getMessage()); // Log error server-side
            echo "<p class='error-msg'>An error occurred while adding the card. Please try again later.</p>";
        }
    }
}

// Fetch card data from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM cards ORDER BY date_created DESC");
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage()); // Log error server-side
    echo "<p class='error-msg'>An error occurred while fetching card data. Please try again later.</p>";
    $cards = []; // Initialize as an empty array to prevent undefined variable warning
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cards</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php include isAdmin() ? '../includes/admin-navbar.php' : '../includes/user-navbar.php'; ?>

<div class="container">
    <h1>Manage Cards</h1>

    <!-- Form to Add New Card (Admin Only) -->
    <?php if (isAdmin()): ?>
    <form method="POST" action="cards.php">
        <input type="text" name="card_number" placeholder="Card Number" required>
        <input type="text" name="expiration" placeholder="Expiration (MM/YYYY)" required>
        <input type="text" name="cc_type" placeholder="CC Type (Visa, MasterCard...)" required>
        <input type="text" name="bin" placeholder="BIN" required>
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="state" placeholder="State" required>
        <input type="text" name="zip" placeholder="Zip" required>
        <input type="text" name="country" placeholder="Country" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <button type="submit" name="add_card" class="action-btn"><i class="fas fa-plus-circle"></i> Add Card</button>
    </form>
    <?php endif; ?>

    <!-- Cards Table -->
    <table>
        <thead>
            <tr>
                <th>Card Number</th>
                <th>Expiration</th>
                <th>CC Info</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Country</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cards)): ?>
                <?php foreach ($cards as $card): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($card['is_purchased'] ? $card['card_number'] : $card['card_number_masked']); ?></td>
                        <td><?php echo htmlspecialchars($card['expiration']); ?></td>
                        <td><?php echo htmlspecialchars($card['cc_type']) . " / " . htmlspecialchars($card['bin']); ?></td>
                        <td><?php echo htmlspecialchars($card['city']); ?></td>
                        <td><?php echo htmlspecialchars($card['state']); ?></td>
                        <td><?php echo htmlspecialchars($card['zip']); ?></td>
                        <td><?php echo htmlspecialchars($card['country']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($card['price'], 2)); ?></td>
                        <td>
                            <?php if (!$card['is_purchased']): ?>
                                <a href="#" data-card-id="<?php echo $card['id']; ?>" class="action-btn purchase-card"><i class="fas fa-shopping-cart"></i> Purchase</a>
                            <?php else: ?>
                                <span>Purchased</span>
                            <?php endif; ?>
                            <?php if (isAdmin()): ?>
                                <a href="#" data-card-id="<?php echo $card['id']; ?>" class="action-btn delete-card"><i class="fas fa-trash"></i> Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No card data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Existing JavaScript code remains unchanged
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.purchase-card').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cardId = this.dataset.cardId;

            if (confirm('Are you sure you want to purchase this card?')) {
                fetch(`cards.php?purchase=${cardId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    document.querySelectorAll('.delete-card').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cardId = this.dataset.cardId;

            if (confirm('Are you sure you want to delete this card?')) {
                fetch(`cards.php?delete=${cardId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
</script>
</body>
</html>
