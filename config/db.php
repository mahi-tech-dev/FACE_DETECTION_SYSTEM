<?php
$conn = new mysqli("localhost", "root", "", "face_detection_system");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>
