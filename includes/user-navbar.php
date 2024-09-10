<!-- User Navbar -->
<div class="navbar">
    <a href="../pages/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="../pages/bank-logs.php"><i class="fas fa-building"></i> Bank Logs</a>
    <a href="../pages/cards.php"><i class="fas fa-credit-card"></i> Cards</a>
    <a href="../pages/account.php"><i class="fas fa-user"></i> Account</a>
    <a href="../pages/spamming.php"><i class="fas fa-file-alt"></i> Spamming</a>
    <a href="../pages/balance.php"><i class="fas fa-dollar-sign"></i> Balance: <?php echo number_format($_SESSION['balance'] ?? 0, 2); ?> USD</a>
    <a href="#" onclick="showModal('logoutModal')"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to log out?</p>
        <button onclick="closeModal('logoutModal')">Cancel</button>
        <button onclick="window.location.href='../auth/logout.php'">Logout</button>
    </div>
</div>

<script>
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>
