<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$q = mysqli_query($conn, "SELECT email, photo FROM users WHERE username='$username'");
$user = mysqli_fetch_assoc($q);

$email = $user['email'] ?? '';
$photo = $user['photo'] ?? 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; margin:0; padding:0; }

body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color: #f8f8f8;
}

/* MAIN */
.main {
    margin-left: 280px;
    padding: 40px 50px;
    min-height: 100vh;
}

/* CARD */
.card {
    max-width: 720px;
    margin: auto;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(14px);
    border-radius: 22px;
    padding: 35px;
    box-shadow: 0 15px 35px rgba(0,0,0,.4);
}

.card h2 {
    text-align: center;
    color: #fbbf24;
    margin-bottom: 25px;
}

/* PROFILE IMAGE */
.profile-img {
    display: block;
    margin: 0 auto 20px;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid #fbbf24;
    object-fit: cover;
}

/* INFO */
.username {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
}

/* FORMS */
label {
    font-size: 14px;
    color: #e5e7eb;
}

input[type="email"],
input[type="password"],
input[type="file"] {
    width: 100%;
    padding: 13px;
    margin-top: 6px;
    margin-bottom: 18px;
    border-radius: 10px;
    border: none;
    background: rgba(255,255,255,0.12);
    color: #fff;
}

input::placeholder {
    color: #cbd5f5;
}

input:focus {
    outline: 2px solid #fbbf24;
}

/* BUTTONS */
button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg,#fbbf24,#f59e0b);
    color: #111827;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: .3s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(251,191,36,.4);
}

/* SEPARATOR */
hr {
    margin: 35px 0;
    border: none;
    border-top: 1px solid rgba(255,255,255,0.2);
}

/* RESPONSIVE */
@media(max-width:1024px){
    .main{margin-left:0;padding:25px;}
}
</style>
</head>

<body>

<?php include "user_sidebar.php"; ?>

<div class="main">
    <div class="card">
        <h2>👤 My Profile</h2>

        <img class="profile-img" src="<?php echo $photo; ?>" alt="Profile Photo">

        <div class="username">
            Username: <span style="color:#fbbf24;"><?php echo htmlspecialchars($username); ?></span>
        </div>

        <!-- UPDATE EMAIL & PHOTO -->
        <form method="post" enctype="multipart/form-data">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <label>Profile Photo</label>
            <input type="file" name="photo">

            <button name="save">Save Changes</button>
        </form>

        <hr>

        <!-- CHANGE PASSWORD -->
        <form method="post">
            <label>Change Password</label>
            <input type="password" name="newpass" placeholder="New password">
            <input type="password" name="confirmpass" placeholder="Confirm password">

            <button name="pass">Update Password</button>
        </form>

        <?php
        if (isset($_POST['save'])) {
            $email = $_POST['email'];
            $photoPath = $photo;

            if (!empty($_FILES['photo']['name'])) {
                $photoPath = "uploads/".time().$_FILES['photo']['name'];
                move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
            }

            mysqli_query($conn, "UPDATE users SET email='$email', photo='$photoPath' WHERE username='$username'");
            echo "<script>alert('Profile updated successfully');location.reload();</script>";
        }

        if (isset($_POST['pass'])) {
            $newpass = trim($_POST['newpass']);
            $confirmpass = trim($_POST['confirmpass']);

            if (empty($newpass) || empty($confirmpass)) {
                echo "<script>alert('Please fill both password fields');</script>";
            } elseif ($newpass !== $confirmpass) {
                echo "<script>alert('Passwords do not match');</script>";
            } else {
                $p = password_hash($newpass, PASSWORD_DEFAULT);
                $update = mysqli_query($conn, "UPDATE users SET password='$p' WHERE username='$username'");

                if ($update) {
                    echo "<script>alert('Password updated successfully');</script>";
                } else {
                    echo "<script>alert('Password update failed');</script>";
                }
            }
        }
        ?>
    </div>
</div>

</body>
</html>
