<?php
$conn = mysqli_connect("localhost", "root", "", "face_detection_db");

if (!$conn) {
    die("Database connection failed");
}
