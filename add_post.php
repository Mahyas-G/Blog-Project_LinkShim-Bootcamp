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
$imagePath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = $_SESSION['user']['username'];
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($content)) {
        $errors[] = "Content is required.";
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageResult = handleImageUpload($_FILES['image']);
        if (!empty($imageResult['errors'])) {
            $errors = array_merge($errors, $imageResult['errors']);
        } else {
            $imagePath = $imageResult['path'];
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO posts (title, content, author, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $title, $content, $author, $imagePath);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Post added successfully!";
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
            
            // Clean up uploaded image if database insert failed
            if (!empty($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);
            }
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
    <style>
        .error-message {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background: #ffe6e6;
            border-radius: 5px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            padding: 10px;
            background: #e6ffe6;
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 150px;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Add New Post</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <form method="POST" enctype="multipart/form-data">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
            </div>

            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($content) ?></textarea>
            </div>

            <div>
                <label for="image">Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                <small>Max size: 2MB. Formats: JPG, PNG, GIF</small>
            </div>

            <div>
                <input type="submit" value="Create Post">
            </div>
        </form>
    </div>
</body>
</html>
