<?php
session_start();
include "config/db.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows == 1) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "INVALID LOGIN";
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Admin Login</h2>

<form method="POST">
    <input name="username" placeholder="Username" required><br><br>
    <input name="password" type="password" placeholder="Password" required><br><br>
    <button name="login">Login</button>
</form>

</body>
</html>
