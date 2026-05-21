<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Access denied");
}

if (!isset($_POST['ids'])) {
    header("Location: view_persons.php");
    exit();
}

$ids = $_POST['ids'];

foreach ($ids as $id) {
    $id = (int)$id;
    $conn->query("DELETE FROM persons WHERE id=$id");
}

header("Location: view_persons.php");
exit();
