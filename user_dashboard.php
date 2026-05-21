<?php
session_start();
include "config/db.php";

// User session check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<style>
/* ===== Base ===== */
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Montserrat', sans-serif; background: linear-gradient(135deg, #0f172a, #1e293b); color: #f8f8f8; }

/* ===== Sidebar ===== */
.sidebar {
    position: fixed;
    left: 0; top: 0;
    width: 260px;
    height: 100vh;
    background: #111827;
    display: flex;
    flex-direction: column;
    padding: 30px 20px;
    box-shadow: 5px 0 15px rgba(0,0,0,0.3);
}
.sidebar h2 {
    text-align: center;
    font-size: 26px;
    margin-bottom: 40px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #fbbf24;
}
.sidebar a {
    color: #f8f8f8;
    text-decoration: none;
    padding: 14px 18px;
    margin: 8px 0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    transition: 0.3s;
}
.sidebar a i { font-size: 18px; min-width: 22px; }
.sidebar a:hover, .sidebar a.active {
    background: #1f2937;
    box-shadow: 0 0 10px #fbbf24;
    color: #fbbf24;
}

/* ===== Main Content ===== */
.main {
    margin-left: 280px;
    padding: 40px 50px;
    min-height: 100vh;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}
.header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #fbbf24;
}
.header .profile {
    display: flex;
    align-items: center;
    gap: 15px;
}
.header .profile img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid #fbbf24;
}
.header .profile span {
    font-weight: 600;
    color: #f8f8f8;
}

/* ===== Glass Cards ===== */
.card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
}
.card {
    flex: 1 1 220px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 25px rgba(0,0,0,0.3);
    transition: all 0.4s;
}
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 35px rgba(251,191,36,0.5);
}
.card i {
    font-size: 3rem;
    color: #fbbf24;
    margin-bottom: 15px;
}
.card h3 {
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: 600;
    color: #f8f8f8;
}
.card a.card-btn {
    display: inline-block;
    padding: 8px 18px;
    border-radius: 10px;
    background: rgba(251,191,36,0.15);
    color: #fbbf24;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
.card a.card-btn:hover {
    background: rgba(251,191,36,0.35);
}

/* ===== Optional Floating Shapes ===== */
body::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(251,191,36,0.2), transparent 70%);
    top: -100px; left: -150px;
    border-radius: 50%;
    z-index: 0;
}
body::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(37,99,235,0.2), transparent 70%);
    bottom: -80px; right: -100px;
    border-radius: 50%;
    z-index: 0;
}

/* ===== Responsive ===== */
@media(max-width: 1024px) {
    .main { margin-left: 0; padding: 25px; }
    .sidebar { width: 100%; height: auto; flex-direction: row; overflow-x: auto; }
    .sidebar a { flex: 1; text-align: center; margin: 0 5px; }
    .card-container { flex-direction: column; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>User Panel</h2>
    <a href="user_dashboard.php" class="active"><i class="fa fa-house"></i> Home</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View Records</a>
    <a href="user_match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="user_match_history.php"><i class="fa fa-clock-rotate-left"></i> Match History</a>
    <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="help.php"><i class="fa fa-question-circle"></i> Help</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
        <div class="profile">
            <img src="https://img.icons8.com/color/96/000000/user-male-circle.png" alt="User Profile">
            <span><?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="card-container">

        <div class="card">
            <i class="fa fa-users"></i>
            <h3>View Records</h3>
            <a href="view_persons.php" class="card-btn">Go</a>
        </div>

        <div class="card">
            <i class="fa fa-face-smile"></i>
            <h3>Match Face</h3>
            <a href="user_match_face.php" class="card-btn">Go</a>
        </div>

        <div class="card">
            <i class="fa fa-clock-rotate-left"></i>
            <h3>Match History</h3>
            <a href="user_match_history.php" class="card-btn">Go</a>
        </div>

        <div class="card">
            <i class="fa fa-user"></i>
            <h3>Profile</h3>
            <a href="profile.php" class="card-btn">Go</a>
        </div>

        <div class="card">
            <i class="fa fa-question-circle"></i>
            <h3>Help</h3>
            <a href="help.php" class="card-btn">Go</a>
        </div>

    </div>

</div>

</body>
</html>
