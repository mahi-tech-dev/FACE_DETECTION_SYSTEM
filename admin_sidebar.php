<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['username'] ?? 'Admin';
?>

<div class="sidebar">
    <h2>Admin Panel</h2>

    <p class="admin-name">
        <i class="fa fa-user-shield"></i>
        <?php echo htmlspecialchars($admin_name); ?>
    </p>

    <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
        <i class="fa fa-house"></i> Home
    </a>

    <a href="add_person.php" class="<?= $current_page == 'add_person.php' ? 'active' : '' ?>">
        <i class="fa fa-user-plus"></i> Add Person
    </a>

    <a href="view_persons.php" class="<?= $current_page == 'view_persons.php' ? 'active' : '' ?>">
        <i class="fa fa-users"></i> View All Records
    </a>

    <a href="match_face.php" class="<?= $current_page == 'match_face.php' ? 'active' : '' ?>">
        <i class="fa fa-face-smile"></i> Match Face
    </a>

    <a href="admin_match_history.php" class="<?= $current_page == 'admin_match_history.php' ? 'active' : '' ?>">
        <i class="fa fa-history"></i> Admin Match History
    </a>

    <!-- 🔹 NEW FEATURE ADDED HERE -->
    <a href="get_person_image.php" class="<?= $current_page == 'get_person_image.php' ? 'active' : '' ?>">
        <i class="fa fa-image"></i> Get Person Image
    </a>
    <!-- 🔹 END -->

    <a href="manage_users.php" class="<?= $current_page == 'manage_users.php' ? 'active' : '' ?>">
        <i class="fa fa-user-cog"></i> Manage Users
    </a>

    <a href="logout.php">
        <i class="fa fa-right-from-bracket"></i> Logout
    </a>
</div>
