<!-- Admin Navbar -->
<div class="navbar">
    <a href="../pages/admin-dashboard.php"><i class="fas fa-cogs"></i> Dashboard</a>
    <a href="../pages/manage-users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="../pages/manage-bank-logs.php"><i class="fas fa-building"></i> Manage Bank Logs</a>
    <a href="../pages/manage-cards.php"><i class="fas fa-credit-card"></i> Manage Cards</a>
    <a href="../pages/admin-reports.php"><i class="fas fa-chart-line"></i> Reports</a>
<a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
