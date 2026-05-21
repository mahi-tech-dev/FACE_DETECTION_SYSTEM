<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    die("Access Denied");
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("
    SELECT * 
    FROM match_history 
    WHERE username = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Match History</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* ===== Base ===== */
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family:'Montserrat',sans-serif; background: linear-gradient(135deg,#0f172a,#1e293b); color:#f8f8f8; }

/* ===== Sidebar ===== */
.sidebar {
    position: fixed;
    width: 260px;
    height: 100vh;
    background: #111827;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
}
.sidebar h2 { text-align:center; color:#fbbf24; margin-bottom:40px; }
.sidebar a { color:#fff; text-decoration:none; padding:14px 18px; margin:8px 0; border-radius:12px; display:flex; align-items:center; gap:12px; transition:.3s; }
.sidebar a:hover, .sidebar a.active { background:#1f2937; color:#fbbf24; box-shadow:0 0 10px rgba(251,191,36,.4); }

/* ===== Main Content ===== */
.main {
    margin-left: 280px;
    padding: 40px 50px;
    min-height: 100vh;
}

/* ===== Header ===== */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.header h1 { color:#fbbf24; font-size:32px; }
.header .profile { display:flex; align-items:center; gap:12px; }
.header .profile img { width:45px; border-radius:50%; border:2px solid #fbbf24; }

/* ===== Card ===== */
.card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 30px rgba(0,0,0,.35);
}

/* ===== Table ===== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    padding: 14px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    text-align: center;
    color: #f8f8f8;
}
th {
    background: rgba(251,191,36,0.8);
    color: #111827;
    font-weight: 600;
}
.match { color: #4ade80; font-weight: bold; }
.nomatch { color: #f87171; font-weight: bold; }

/* ===== Responsive ===== */
@media(max-width:1024px){
    .main{margin-left:0;padding:25px;}
    .sidebar{width:100%;height:auto;flex-direction:row;overflow-x:auto;}
    .sidebar a{flex:1;text-align:center;margin:0 5px;}
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>User Panel</h2>
    <a href="user_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View Records</a>
    <a href="user_match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="user_match_history.php" class="active"><i class="fa fa-clock-rotate-left"></i> Match History</a>
    <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="help.php"><i class="fa fa-question-circle"></i> Help</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main -->
<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <div class="profile">
            <img src="https://img.icons8.com/color/96/000000/user-male-circle.png" alt="Profile">
            <span><?php echo htmlspecialchars($username); ?></span>
        </div>
    </div>

    <div class="card">
        <h2 style="text-align:center;">🕒 My Match History</h2>

        <table>
            <tr>
                <th>#</th>
                <th>Matched Person</th>
                <th>Distance</th>
                <th>Status</th>
                <th>Date</th>
            </tr>

            <?php
            $i = 1;
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['matched_person'] ?? '-') ?></td>
                <td><?= $row['match_distance'] !== null ? number_format($row['match_distance'], 4) : '-' ?></td>
                <td class="<?= $row['match_status'] === 'Matched' ? 'match' : 'nomatch' ?>">
                    <?= htmlspecialchars($row['match_status']) ?>
                </td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>
