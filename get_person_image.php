<?php
include "config/db.php";

if (!isset($_GET['name'])) {
    exit("No name provided");
}

$name = mysqli_real_escape_string($conn, $_GET['name']);

$q = mysqli_query($conn, "
    SELECT name, age, photo 
    FROM persons 
    WHERE name = '$name'
    LIMIT 1
");

if ($row = mysqli_fetch_assoc($q)) {
    echo json_encode([
        'status' => 'success',
        'name' => $row['name'],
        'age' => $row['age'],
        'photo' => $row['photo']
    ]);
} else {
    echo json_encode(['status' => 'not_found']);
}
