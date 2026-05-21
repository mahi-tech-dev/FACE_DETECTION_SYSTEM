<?php
session_start();
include "config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    exit;
}

$username = $data['username'];
$matched_person = $data['matched_person'];
$distance = $data['distance'];
$status = $data['status'];

$match_source = isset($data['match_source']) && !empty($data['match_source']) 
                ? $data['match_source'] 
                : 'UPLOAD';

/* Insert match history for BOTH admin and users */
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO match_history (username, matched_person, match_distance, match_status, match_source, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssdss",
    $username,
    $matched_person,
    $distance,
    $status,
    $match_source
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
