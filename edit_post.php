<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/image_functions.php';
require_once 'includes/db.php'; 

$errors = [];
$title = '';
$content = '';
$currentImage = '';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : null;


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $postId) {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $postId);
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if ($post) {
        // Verify post ownership
        if ($post['author'] !== $_SESSION['user']['username']) {
            $_SESSION['error'] = "You can only edit your own posts.";
            header("Location: dashboard.php");
            exit;
        }
        $title = $post['title'];
        $content = $post['content'];
        $currentImage = $post['image'];
    } else {
        $_SESSION['error'] = "Post not found.";
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
    if (empty($title) || empty($content)) {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageResult = handleImageUpload($_FILES['image']);
        
        if (!empty($imageResult['errors'])) {
            $errors = array_merge($errors, $imageResult['errors']);
        } else {
            // Remove old image if new one is uploaded successfully
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
        $sql = "UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ? AND author = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssis", $title, $content, $currentImage, $postId, $_SESSION['user']['username']);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Post updated successfully!";
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .current-image {
            max-width: 100%;
            max-height: 300px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .image-options {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        textarea {
            min-height: 200px;
        }
        button[type="submit"] {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button[type="submit"]:hover {
            background: #218838;
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
            <input type="hidden" name="id" value="<?= htmlspecialchars($postId) ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($currentImage) ?>">
            
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
            </div>
            
            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($content) ?></textarea>
            </div>
            
            <div class="image-options">
                <?php if (!empty($currentImage)): ?>
                    <label>Current Image:</label>
                    <img src="<?= htmlspecialchars($currentImage) ?>" class="current-image">
                    <label>
                        <input type="checkbox" name="remove_image" value="1"> 
                        Remove current image
                    </label>
                <?php endif; ?>
                
                <label for="image">New Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                <small>Max size: 2MB. Formats: JPG, PNG, GIF</small>
            </div>
            
            <button type="submit">Update Post</button>
        </form>
    </div>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close(); 
}
?>