<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/image_functions.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$title = '';
$content = '';
$currentImage = '';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch post details for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $postId) {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if ($post) {
        if ($post['author'] !== $_SESSION['user']['username']) {
            die("You can only edit your own posts.");
        }
        $title = $post['title'];
        $content = $post['content'];
        $currentImage = $post['image'];
    } else {
        header("Location: dashboard.php");
        exit;
    }
}

// Update post details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $currentImage = $_POST['current_image'] ?? '';
    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';

    // Validate inputs
    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageResult = handleImageUpload($_FILES['image']);
        if (!empty($imageResult['errors'])) {
            $errors = array_merge($errors, $imageResult['errors']);
        } else {
            // Remove old image if new one is uploaded
            if (!empty($currentImage) && file_exists($currentImage)) {
                unlink($currentImage);
            }
            $currentImage = $imageResult['path'];
        }
    } elseif ($removeImage) {
        // Remove the current image if requested
        if (!empty($currentImage) && file_exists($currentImage)) {
            unlink($currentImage);
        }
        $currentImage = '';
    }

    // Update the database if no errors
    if (empty($errors)) {
        $sql = "UPDATE posts SET title = ?, content = ?, image = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $content, $currentImage, $postId);

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
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .current-image {
            max-width: 100%;
            margin: 10px 0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .image-options {
            margin: 20px 0;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <?php
    include 'includes/header.php';
    include 'includes/db.php';
    ?>

<div class="container">
    <h2>Edit Post</h2>

    <?php foreach ($errors as $error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($postId) ?>">
        <input type="hidden" name="current_image" value="<?= htmlspecialchars($currentImage) ?>">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>">

        <label>Content:</label>
        <textarea name="content" rows="5"><?= htmlspecialchars($content) ?></textarea>

        <div class="image-options">
            <?php if (!empty($currentImage)): ?>
                <label>Current Image:</label><br>
                <img src="<?= htmlspecialchars($currentImage) ?>" class="current-image"><br>
                <label>
                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                </label><br><br>
            <?php endif; ?>

            <label>New Image (optional):</label>
            <input type="file" name="image" accept="image/jpeg, image/png, image/gif">
            <small>Leave blank to keep current image</small>
        </div>

        <button type="submit">Update</button>
    </form>
</div>

</body>
</html>