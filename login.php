<?php
session_start();
include "config/db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM users WHERE username=?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPass = $row['password'];

        if (password_verify($password, $dbPass)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        }
        elseif (md5($password) === $dbPass) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $update->bind_param("ss", $newHash, $username);
            $update->execute();

            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        }
        else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* ===== Reset & Base ===== */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #4f46e5, #2563eb);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* ===== Card Container ===== */
.login-box {
    background: #fff;
    padding: 55px 45px;
    width: 400px;
    border-radius: 18px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    position: relative;
    overflow: hidden;
    text-align: center;
}

/* Animated background circles */
.login-box::before,
.login-box::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(37, 99, 235, 0.15);
    z-index: 0;
}
.login-box::before {
    width: 220px; height: 220px;
    top: -60px; left: -60px;
}
.login-box::after {
    width: 320px; height: 320px;
    bottom: -120px; right: -120px;
}

/* ===== Logo/Header ===== */
.logo {
    margin-bottom: 25px;
    position: relative;
    z-index: 1;
}
.logo img {
    width: 70px;
    margin-bottom: 10px;
}
.logo h1 {
    font-size: 24px;
    color: #111827;
    font-weight: 600;
}

/* ===== Heading ===== */
.login-box h2 {
    text-align: center;
    color: #111827;
    margin-bottom: 25px;
    position: relative;
    z-index: 1;
    font-size: 22px;
}

/* ===== Input Fields ===== */
.input-group {
    position: relative;
    margin-bottom: 20px;
    z-index: 1;
}
.input-group i {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 16px;
}
.input-group input {
    width: 100%;
    padding: 14px 14px 14px 45px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    outline: none;
    font-size: 15px;
    transition: all 0.3s;
}
.input-group input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.3);
}

/* ===== Button ===== */
button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #2563eb;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
button i {
    margin-right: 8px;
}
button:hover {
    background: #1e40af;
}

/* ===== Forgot Password ===== */
.forgot {
    margin-top: 12px;
    text-align: right;
    font-size: 14px;
    position: relative;
    z-index: 1;
}
.forgot a {
    color: #2563eb;
    text-decoration: none;
    transition: 0.3s;
}
.forgot a:hover {
    text-decoration: underline;
}

/* ===== Error Message ===== */
.error {
    margin-top: 18px;
    text-align: center;
    color: #ef4444;
    font-weight: 500;
    z-index: 1;
    position: relative;
}

/* ===== Responsive ===== */
@media(max-width: 420px) {
    .login-box { width: 90%; padding: 40px 25px; }
}
</style>
</head>
<body>

<div class="login-box">
    <div class="logo">
        <img src="https://img.icons8.com/color/96/000000/face-id.png" alt="Logo">
        <h1>Face Detection System</h1>
    </div>

    <h2><i class="fa fa-user-lock"></i> Login</h2>

    <form method="post">
        <div class="input-group">
            <i class="fa fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login">
            <i class="fa fa-right-to-bracket"></i> Login
        </button>
    </form>

    <div class="forgot">
        <a href="#">Forgot Password?</a>
    </div>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
