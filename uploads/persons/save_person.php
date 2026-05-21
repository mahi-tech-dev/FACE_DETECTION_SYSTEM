<?php
include "../config/db.php";

$name = $_POST['name'];
$img = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp, "../uploads/persons/$img");

$conn->query("INSERT INTO persons (name, image) VALUES ('$name','$img')");
echo "Person added successfully";
?>
