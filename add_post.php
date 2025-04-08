<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = $destination;
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        }
    }

    if (empty($errors)) {
        $posts = [];
        if (file_exists("data/posts.json")) {
            $posts = json_decode(file_get_contents("data/posts.json"), true);
        }

        $newId = count($posts) > 0 ? end($posts)['id'] + 1 : 1;

        $newPost = [
            "id" => $newId,
            "title" => $title,
            "content" => $content,
            "author" => $_SESSION['user']['username'],
            "created_at" => date("Y-m-d H:i"),
            "image" => $imagePath
        ];

        $posts[] = $newPost;

        file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<?php include 'includes/header.php'; ?>

<h2>Add New Post</h2>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= $error ?></p>
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
