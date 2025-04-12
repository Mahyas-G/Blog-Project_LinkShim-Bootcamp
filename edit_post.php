<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/image_functions.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

$found = false;
foreach ($posts as $index => $post) {
    if ($post['id'] === $postId) {
        if ($post['author'] !== $_SESSION['user']['username']) {
            die("You can only edit your own posts.");
        }
        $found = true;
        $postIndex = $index;
        break;
    }
}

if (!$found) {
    die("Post not found.");
}

$title = $posts[$postIndex]['title'];
$content = $posts[$postIndex]['content'];
$currentImage = $posts[$postIndex]['image'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageResult = handleImageUpload($_FILES['image']);
        if (!empty($imageResult['errors'])) {
            $errors = array_merge($errors, $imageResult['errors']);
        } else {
            // Delete old image if exists
            if (!empty($currentImage) && file_exists($currentImage)) {
                unlink($currentImage);
            }
            $currentImage = $imageResult['path'];
        }
    } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($currentImage) && file_exists($currentImage)) {
            unlink($currentImage);
        }
        $currentImage = '';
    }

    if (empty($errors)) {
        $posts[$postIndex]['title'] = $title;
        $posts[$postIndex]['content'] = $content;
        $posts[$postIndex]['image'] = $currentImage;

        file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
        header("Location: dashboard.php");
        exit;
    }
}
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

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Edit Post</h2>

    <?php foreach ($errors as $error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
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
