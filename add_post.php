<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/image_functions.php';

// Initialize variables
$errors = [];
$title = '';
$content = '';
$imagePath = ''; // Store uploaded image path if provided

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = $_SESSION['user']['username']; // Fetch the logged-in user as author

    // Validate form inputs
    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageResult = handleImageUpload($_FILES['image']);
        if (!empty($imageResult['errors'])) {
            $errors = array_merge($errors, $imageResult['errors']);
        } else {
            $imagePath = $imageResult['path']; // Save the uploaded image path
        }
    }

    // Insert post into the database if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO posts (title, content, author, image, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $content, $author, $imagePath);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php
    include 'includes/header.php';
    include 'includes/db.php';
    ?>

    <h2>Add New Post</h2>

    <!-- Display validation errors -->
    <?php foreach ($errors as $error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br><br>

        <label>Content:</label><br>
        <textarea name="content" rows="5" cols="40"><?= htmlspecialchars($content) ?></textarea><br><br>

        <label>Image (optional):</label><br>
        <input type="file" name="image" accept="image/jpeg, image/png, image/gif"><br><br>

        <input type="submit" value="Post">
    </form>
</div>
</body>
</html>