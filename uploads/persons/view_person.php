<?php
include "../config/db.php";
$q = $conn->query("SELECT * FROM persons");

while ($r = $q->fetch_assoc()) {
    echo "<p>{$r['name']}</p>";
    echo "<img src='../uploads/persons/{$r['image']}' width='120'><hr>";
}
?>
