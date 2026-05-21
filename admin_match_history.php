<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo "Access denied!";
    exit();
}

$admin_name = $_SESSION['username'];

// Fetch match history
$history = [];
$q = mysqli_query($conn, "
    SELECT id, username, matched_person, match_distance, match_status, match_source, created_at
    FROM match_history
    ORDER BY created_at DESC
");
while ($row = mysqli_fetch_assoc($q)) {

    // 🔧 FIX: If admin match has no username, assign admin name
    if (empty($row['username'])) {
        $row['username'] = $admin_name;
    }

    $history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Match History</title>
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
    margin-bottom: 30px;
}
.header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #fbbf24;
}
.header h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #f8f8f8;
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

/* ===== Glass Table ===== */
.table-container {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 12px 25px rgba(0,0,0,0.3);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
}
table th, table td {
    padding: 12px 15px;
    text-align: left;
}
table th {
    background: #2563eb;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
}
table tr:nth-child(even) { background: rgba(255,255,255,0.05); }
table tr:nth-child(odd) { background: rgba(255,255,255,0.1); }
.status-match { color: #4ade80; font-weight: bold; }
.status-no { color: #f87171; font-weight: bold; }

/* ===== Responsive ===== */
@media(max-width: 1024px) {
    .main { margin-left: 0; padding: 25px; }
    .sidebar { width: 100%; height: auto; flex-direction: row; overflow-x: auto; }
    .sidebar a { flex: 1; text-align: center; margin: 0 5px; }
    .table-container { padding: 15px; }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View All Records</a>
    <a href="match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php" class="active"><i class="fa fa-clock"></i> Match History</a>
    <a href="manage_users.php"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>
        <div class="profile">
            <img src="https://img.icons8.com/color/96/000000/administrator-male.png">
            <span><?php echo htmlspecialchars($admin_name); ?></span>
        </div>
    </div>

    <h2>All Users Match History</h2>

    <?php if(count($history) == 0): ?>
        <p>No match history found.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Matched Person</th>
            <th>Distance</th>
            <th>Status</th>
            <th>Source</th>
            <th>Date & Time</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($history as $h): ?>
        <tr>
            <td><?php echo $h['id']; ?></td>
            <td><?php echo htmlspecialchars($h['username']); ?></td>
            <td><?php echo htmlspecialchars($h['matched_person']); ?></td>
            <td><?php echo number_format($h['match_distance'], 3); ?></td>
            <td class="<?php echo ($h['match_status']=='Match Found') ? 'status-match' : 'status-no'; ?>">
            <?php echo htmlspecialchars($h['match_status']); ?>
            </td>
            <td><?php echo htmlspecialchars($h['match_source']); ?></td>
            <td><?php echo $h['created_at']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
