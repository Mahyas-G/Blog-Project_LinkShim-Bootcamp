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
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Delete old image if exists
            if (!empty($currentImage) && file_exists($currentImage)) {
                unlink($currentImage);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $currentImage = $destination;
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        }
    } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        // Remove existing image if requested
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
            max-width: 300px;
            margin: 10px 0;
        }
        .image-options {
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="container">
<?php include 'includes/header.php'; ?>

<h2>Edit Post</h2>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="5" cols="40"><?= htmlspecialchars($content) ?></textarea><br><br><div class="image-options">
        <?php if (!empty($currentImage)): ?>
            <label>Current Image:</label><br>
            <img src="<?= htmlspecialchars($currentImage) ?>" class="current-image"><br>
            <label>
                <input type="checkbox" name="remove_image" value="1"> Remove current image
            </label><br><br>
        <?php endif; ?>
        
        <label>New Image (optional):</label><br>
        <input type="file" name="image" accept="image/jpeg, image/png, image/gif"><br>
        <small>Leave blank to keep current image</small>
    </div><br>

    <input type="submit" value="Update">
</form>
</div>
</body>
</html>
