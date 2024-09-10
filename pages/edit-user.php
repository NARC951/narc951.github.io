<?php
require '../includes/functions.php'; // Include functions.php

if (!isset($_SESSION['username']) || !isAdmin()) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<p class='error-msg'>User not found.</p>";
        exit();
    }
}

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    try {
        $stmtUpdate = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
        $stmtUpdate->execute(['username' => $username, 'email' => $email, 'role' => $role, 'id' => $userId]);
        echo "<p class='success-msg'>User updated successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='error-msg'>Error updating user: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="navbar">
    <a href="admin-dashboard.php"><i class="fas fa-cogs"></i> Admin Dashboard</a>
    <a href="manage-users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage-bank-logs.php"><i class="fas fa-building"></i> Manage Bank Logs</a>
    <a href="manage-cards.php"><i class="fas fa-credit-card"></i> Manage Cards</a>
    <a href="#" onclick="showModal('logoutModal')"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="container">
    <h1>Edit User</h1>
    <form method="POST" action="edit-user.php?id=<?php echo $userId; ?>">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="User" <?php echo $user['role'] === 'User' ? 'selected' : ''; ?>>User</option>
            <option value="Admin" <?php echo $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="submit" name="update_user" class="action-btn"><i class="fas fa-save"></i> Save Changes</button>
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

<script src="../assets/js/script.js"></script>
</body>
</html>
