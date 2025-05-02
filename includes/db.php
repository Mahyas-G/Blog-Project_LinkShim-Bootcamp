<?php
$servername = "localhost";
$username = "root";
$password = "44413138";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>