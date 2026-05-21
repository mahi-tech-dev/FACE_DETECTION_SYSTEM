<?php
session_start();
include "config/db.php";

// Add User logic (simplified)
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}

// Delete User logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: manage_users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'Montserrat',sans-serif;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#f8f8f8;
}

/* ===== Sidebar ===== */
.sidebar{
    position:fixed;
    width:260px;
    height:100vh;
    background:#111827;
    padding:30px 20px;
}
.sidebar h2{
    text-align:center;
    color:#fbbf24;
    margin-bottom:40px;
    letter-spacing:1px;
}
.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    color:#fff;
    text-decoration:none;
    padding:14px 18px;
    border-radius:12px;
    margin:8px 0;
    transition:.3s;
}
.sidebar a:hover,
.sidebar a.active{
    background:#1f2937;
    color:#fbbf24;
    box-shadow:0 0 10px rgba(251,191,36,.4);
}

/* ===== Main ===== */
.main{
    margin-left:280px;
    padding:40px 50px;
}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.header h1{
    color:#fbbf24;
    font-size:32px;
}
.profile{
    display:flex;
    align-items:center;
    gap:12px;
}
.profile img{
    width:45px;
    border-radius:50%;
    border:2px solid #fbbf24;
}

/* ===== Glass Cards ===== */
.card{
    background:rgba(255,255,255,.06);
    backdrop-filter:blur(12px);
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 30px rgba(0,0,0,.35);
    margin-bottom:35px;
}
.card h3{
    margin-bottom:20px;
    color:#fbbf24;
}

/* ===== Form ===== */
form input,form select{
    width:100%;
    padding:12px 15px;
    margin-bottom:15px;
    border-radius:10px;
    border:none;
    outline:none;
}
form button{
    background:#2563eb;
    border:none;
    padding:12px 22px;
    border-radius:10px;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}
form button:hover{
    background:#1d4ed8;
}

/* ===== Table ===== */
.table-box{
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
}
table th,table td{
    padding:14px 16px;
    text-align:left;
}
table th{
    background:#2563eb;
    color:#fff;
}
table tr:nth-child(even){
    background:rgba(255,255,255,.04);
}
table tr:hover{
    background:rgba(255,255,255,.08);
}

/* ===== Buttons ===== */
.btn-delete{
    background:#dc2626;
    padding:7px 14px;
    border-radius:8px;
    color:#fff;
    text-decoration:none;
    transition:.3s;
}
.btn-delete:hover{
    background:#b91c1c;
}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .sidebar{position:relative;width:100%;height:auto}
    .main{margin-left:0;padding:25px}
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View Records</a>
    <a href="match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php"><i class="fa fa-clock"></i> Match History</a>
    <a href="manage_users.php" class="active"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Manage Users</h1>
        <div class="profile">
            <img src="https://img.icons8.com/color/96/administrator-male.png">
        </div>
    </div>

    <div class="card">
        <h3>Add New User</h3>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

    <div class="card">
        <h3>Existing Users</h3>
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <a class="btn-delete" href="?delete=<?= $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
