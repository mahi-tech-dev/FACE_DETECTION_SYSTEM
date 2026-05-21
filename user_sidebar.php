<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* ===== SIDEBAR ===== */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #020617, #0f172a);
    padding: 28px 20px;
    box-shadow: 6px 0 25px rgba(0,0,0,0.7);
    z-index: 100;
}

/* TITLE */
.sidebar h2 {
    color: #fbbf24;
    font-size: 26px;
    font-weight: 700;
    text-align: left;
    margin-bottom: 35px;
    letter-spacing: 1px;
}

/* LINKS */
.sidebar a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    margin-bottom: 10px;
    border-radius: 14px;
    color: #e5e7eb;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar a i {
    font-size: 18px;
    min-width: 22px;
}

/* HOVER */
.sidebar a:hover {
    background: rgba(251,191,36,0.15);
    color: #fbbf24;
}

/* ACTIVE */
.sidebar a.active {
    background: rgba(251,191,36,0.18);
    color: #fbbf24;
    box-shadow: inset 0 0 0 1px rgba(251,191,36,0.4),
                0 0 18px rgba(251,191,36,0.45);
}

/* LOGOUT */
.sidebar a.logout {
    margin-top: 28px;
    background: rgba(239,68,68,0.15);
    color: #fca5a5;
}
.sidebar a.logout:hover {
    background: rgba(239,68,68,0.35);
    color: #fff;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        display: flex;
        overflow-x: auto;
    }
    .sidebar h2 {
        display: none;
    }
    .sidebar a {
        flex: 1;
        justify-content: center;
        white-space: nowrap;
        margin: 6px;
    }
}
</style>

<div class="sidebar">
    <h2>User Panel</h2>

    <a href="user_dashboard.php" class="<?= $current_page=='user_dashboard.php'?'active':'' ?>">
        <i class="fa fa-house"></i> Home
    </a>

    <a href="view_persons.php" class="<?= $current_page=='view_persons.php'?'active':'' ?>">
        <i class="fa fa-users"></i> View Records
    </a>

    <a href="user_match_face.php" class="<?= $current_page=='user_match_face.php'?'active':'' ?>">
        <i class="fa fa-face-smile"></i> Match Face
    </a>

    <a href="user_match_history.php" class="<?= $current_page=='user_match_history.php'?'active':'' ?>">
        <i class="fa fa-clock-rotate-left"></i> Match History
    </a>

    <a href="profile.php" class="<?= $current_page=='profile.php'?'active':'' ?>">
        <i class="fa fa-user"></i> Profile
    </a>

    <a href="help.php" class="<?= $current_page=='help.php'?'active':'' ?>">
        <i class="fa fa-question-circle"></i> Help
    </a>

    <a href="logout.php" class="logout">
        <i class="fa fa-right-from-bracket"></i> Logout
    </a>
</div>
