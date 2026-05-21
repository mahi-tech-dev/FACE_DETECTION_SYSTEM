<?php
session_start();

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
<title>Help & Guide</title>

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

/* HEADER */
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.header h1 {
    color:#fbbf24;
    font-size:32px;
}

/* CARD */
.card {
    max-width: 900px;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(14px);
    border-radius: 22px;
    padding: 35px 40px;
    box-shadow: 0 15px 35px rgba(0,0,0,.4);
}

/* SECTION */
.section {
    margin-bottom: 30px;
}
.section h3 {
    color: #fbbf24;
    margin-bottom: 10px;
}
.section ul {
    margin-left: 20px;
}
.section li {
    margin: 8px 0;
    line-height: 1.6;
}

/* ICON */
.section h3 i {
    margin-right: 8px;
}

/* FOOTER TEXT */
.help-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.2);
    text-align: center;
    color: #e5e7eb;
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

    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
    </div>

    <div class="card">
        <h2 style="text-align:center;color:#fbbf24;margin-bottom:30px;">
            🆘 Help & User Guide
        </h2>

        <div class="section">
            <h3><i class="fa fa-camera"></i> How Face Matching Works</h3>
            <ul>
                <li>Allow camera permission when prompted</li>
                <li>Keep your face straight and clearly visible</li>
                <li>Good lighting improves recognition accuracy</li>
            </ul>
        </div>

        <div class="section">
            <h3><i class="fa fa-circle-xmark"></i> If Face Is Not Matching</h3>
            <ul>
                <li>Ensure proper lighting</li>
                <li>Do not cover your face (mask, cap, sunglasses)</li>
                <li>Stay still for a few seconds</li>
            </ul>
        </div>

        <div class="section">
            <h3><i class="fa fa-clock-rotate-left"></i> Match History</h3>
            <ul>
                <li>All successful and failed attempts are saved</li>
                <li>Date, time, and match result are recorded automatically</li>
            </ul>
        </div>

        <div class="section">
            <h3><i class="fa fa-shield-halved"></i> Account & Security</h3>
            <ul>
                <li>You can change your password from the Profile page</li>
                <li>Your facial data is stored securely</li>
            </ul>
        </div>

        <div class="help-footer">
            <p><i class="fa fa-headset"></i> Need more help?</p>
            <p>Contact the system administrator or project supervisor.</p>
        </div>
    </div>

</div>

</body>
</html>
