<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
?>

<?php
session_start();
echo "LOGIN SUCCESS. DASHBOARD OPENED.";
?>
