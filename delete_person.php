<?php
session_start();
include "config/db.php";

/* ADMIN CHECK */
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

/* ID CHECK */
if (!isset($_GET['id'])) {
    die("Invalid request!");
}

$id = intval($_GET['id']);

/* FETCH PHOTO NAME */
$stmt = $conn->prepare("SELECT photo FROM persons WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found!");
}

$row = $result->fetch_assoc();
$photo = $row['photo'];

/* DELETE RECORD */
$del = $conn->prepare("DELETE FROM persons WHERE id = ?");
$del->bind_param("i", $id);

if ($del->execute()) {

    // Delete photo from folder (optional but recommended)
    if (!empty($photo) && file_exists("uploads/persons/" . $photo)) {
        unlink("uploads/persons/" . $photo);
    }

    header("Location: view_persons.php");
    exit();

} else {
    echo "Delete failed!";
}
?>
