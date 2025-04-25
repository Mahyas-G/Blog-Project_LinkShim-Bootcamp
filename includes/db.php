<?php
$servername = "localhost";
$username = "root";
$password = "BlogProject12345";
$dbname = "mydb";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
