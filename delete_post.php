<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

include 'includes/db.php';


$servername = "localhost";
$username = "root";
$password = "44413138";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$postId = (int)$_GET['id'];


$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

if ($post['author'] !== $_SESSION['user']['username']) {
    die("You can only delete your own posts.");
}

if (!empty($post['image']) && file_exists($post['image'])) {
    unlink($post['image']);
}

$sql = "DELETE FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postId);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    die("Error: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
