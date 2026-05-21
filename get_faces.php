<?php
include "config/db.php";

$data = [];
$result = mysqli_query($conn, "SELECT name, age, photo FROM persons");

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "name" => $row['name'],
        "age" => $row['age'],
        "image" => $row['photo']
    ];
}

echo json_encode($data);
