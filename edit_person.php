<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo "Access denied!";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM persons WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$person = $result->fetch_assoc();

$message = "";

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $age  = $_POST['age'];

    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $tmp   = $_FILES['photo']['tmp_name'];
        move_uploaded_file($tmp, "uploads/persons/" . $photo);

        $stmt = $conn->prepare(
            "UPDATE persons SET name=?, age=?, photo=? WHERE id=?"
        );
        $stmt->bind_param("sisi", $name, $age, $photo, $id);
    } else {
        $stmt = $conn->prepare(
            "UPDATE persons SET name=?, age=? WHERE id=?"
        );
        $stmt->bind_param("sii", $name, $age, $id);
    }

    if ($stmt->execute()) {
        $message = "Person updated successfully!";
    } else {
        $message = "Error updating person!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Person</title>

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
    left:0;
    top:0;
    width:260px;
    height:100vh;
    background:#111827;
    padding:30px 20px;
    box-shadow:5px 0 15px rgba(0,0,0,0.35);
}

.sidebar h2{
    text-align:center;
    color:#fbbf24;
    font-size:26px;
    margin-bottom:40px;
    font-weight:700;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    color:#f8f8f8;
    text-decoration:none;
    padding:14px 18px;
    margin:8px 0;
    border-radius:12px;
    transition:.3s;
}

.sidebar a:hover{
    background:#1f2937;
    box-shadow:0 0 10px rgba(251,191,36,.6);
    color:#fbbf24;
}

/* ===== Main ===== */

.main{
    margin-left:280px;
    padding:40px 50px;
    min-height:100vh;
}

.main h2{
    font-size:30px;
    color:#fbbf24;
    margin-bottom:25px;
}

/* ===== Glass Card ===== */

.card{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(12px);
    border-radius:20px;
    padding:35px;
    max-width:550px;
    box-shadow:0 15px 35px rgba(0,0,0,.35);
}

/* ===== Form ===== */

.form-group{
    margin-bottom:20px;
}

label{
    font-weight:600;
    margin-bottom:6px;
    display:block;
}

input[type="text"],
input[type="number"],
input[type="file"]{
    width:100%;
    padding:12px 15px;
    border-radius:12px;
    border:none;
    outline:none;
    background:rgba(255,255,255,0.15);
    color:#fff;
}

input::placeholder{
    color:#cbd5e1;
}

/* ===== Image ===== */

.preview{
    margin-top:10px;
}

.preview img{
    width:120px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,.4);
}

/* ===== Button ===== */

button{
    padding:10px 18px;
    border-radius:10px;
    border:none;
    background:rgba(251,191,36,.2);
    color:#fbbf24;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#fbbf24;
    color:#111827;
}

/* ===== Message ===== */

.success{
    margin-top:15px;
    color:#4ade80;
    font-weight:600;
}

</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fa fa-house"></i> Home</a>
    <a href="add_person.php"><i class="fa fa-user-plus"></i> Add Person</a>
    <a href="view_persons.php"><i class="fa fa-users"></i> View All Records</a>
    <a href="match_face.php"><i class="fa fa-face-smile"></i> Match Face</a>
    <a href="admin_match_history.php"><i class="fa fa-history"></i> Match History</a>
    <a href="manage_users.php"><i class="fa fa-user-cog"></i> Manage Users</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main -->
<div class="main">

<h2>Edit Person</h2>

<div class="card">

<form method="post" enctype="multipart/form-data">

<div class="form-group">
<label>Name</label>
<input type="text" name="name" value="<?php echo $person['name']; ?>" required>
</div>

<div class="form-group">
<label>Age</label>
<input type="number" name="age" value="<?php echo $person['age']; ?>" required>
</div>

<div class="form-group">
<label>Change Photo (optional)</label>
<input type="file" name="photo">
</div>

<div class="preview">
<label>Current Photo</label><br>
<img src="uploads/persons/<?php echo $person['photo']; ?>">
</div>

<br>

<button type="submit" name="update">
<i class="fa fa-save"></i> Update Person
</button>

</form>

<?php if ($message): ?>
<div class="success"><?php echo $message; ?></div>
<?php endif; ?>

</div>
</div>

</body>
</html>