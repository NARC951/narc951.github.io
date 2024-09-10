<?php
require '../includes/functions.php'; // Include functions.php

if (!isset($_SESSION['username']) || !isAdmin()) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Handle adding a new card
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $cardNumber = htmlspecialchars($_POST['card_number']);
    $expiration = htmlspecialchars($_POST['expiration']);
    $ccType = htmlspecialchars($_POST['cc_type']);
    $bin = htmlspecialchars($_POST['bin']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $zip = htmlspecialchars($_POST['zip']);
    $country = htmlspecialchars($_POST['country']);
    $price = floatval($_POST['price']);

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
        echo "<p class='error-msg'>Error adding card: " . $e->getMessage() . "</p>";
    }
}

// Handle card deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $cardId = intval($_GET['delete']);
    try {
        // Delete card from the database
        $stmt = $pdo->prepare("DELETE FROM cards WHERE id = :id");
        $stmt->execute(['id' => $cardId]);
        echo "<p class='success-msg'>Card deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='error-msg'>Error deleting card: " . $e->getMessage() . "</p>";
    }
}

// Fetch card data from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM cards ORDER BY date_created DESC");
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching card data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cards</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php include '../includes/admin-navbar.php'; ?>

<!-- Form to Add New Card -->
<form method="POST" action="manage-cards.php" class="form-section">
    <input type="text" name="card_number" placeholder="Card Number" required>
    <input type="text" name="expiration" placeholder="Expiration (MM/YYYY)" required>
    <input type="text" name="cc_type" placeholder="CC Type (Visa, MasterCard...)" required>
    <input type="text" name="bin" placeholder="BIN" required>
    <input type="text" name="city" placeholder="City" required>
    <input type="text" name="state" placeholder="State" required>
    <input type="text" name="zip" placeholder="Zip" required>
    <input type="text" name="country" placeholder="Country" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <!-- Add icon inside button -->
    <button type="submit" name="add_card" class="action-btn">
        <i class="fas fa-plus-circle"></i> Add Card
    </button>
</form>

<!-- Updated Card Actions with Icons -->
<td>
    <a href="edit-card.php?id=<?php echo $card['id']; ?>" class="action-btn">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="manage-cards.php?delete=<?php echo $card['id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this card?');">
        <i class="fas fa-trash"></i> Delete
    </a>
</td>

    <!-- Cards Table -->
    <table>
        <thead>
            <tr>
                <th>Card Number</th>
                <th>Expire</th>
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
                        <td><?php echo htmlspecialchars($card['card_number_masked']); ?></td>
                        <td><?php echo htmlspecialchars($card['expiration']); ?></td>
                        <td><?php echo htmlspecialchars($card['cc_type']) . " / " . htmlspecialchars($card['bin']); ?></td>
                        <td><?php echo htmlspecialchars($card['city']); ?></td>
                        <td><?php echo htmlspecialchars($card['state']); ?></td>
                        <td><?php echo htmlspecialchars($card['zip']); ?></td>
                        <td><?php echo htmlspecialchars($card['country']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($card['price'], 2)); ?></td>
                        <td>
                            <a href="edit-card.php?id=<?php echo $card['id']; ?>" class="action-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="manage-cards.php?delete=<?php echo $card['id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this card?');"><i class="fas fa-trash"></i> Delete</a>
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

<script src="../assets/js/script.js"></script>
</body>
</html>
