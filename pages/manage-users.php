<?php
require '../includes/functions.php'; // Include functions.php

if (!isset($_SESSION['username']) || !isAdmin()) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    try {
        // Delete user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        echo "<p class='success-msg'>User deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='error-msg'>Error deleting user: " . $e->getMessage() . "</p>";
    }
}

// Fetch all users
$stmt = $pdo->prepare("SELECT id, username, email, role FROM users ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
    <h1>Manage Users</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="action-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="manage-users.php?delete=<?php echo $user['id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
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
