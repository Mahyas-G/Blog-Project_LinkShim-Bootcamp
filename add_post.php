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

    if (empty($errors)) {
        // Load existing posts
        $posts = [];
        if (file_exists("data/posts.json")) {
            $posts = json_decode(file_get_contents("data/posts.json"), true);
        }

        // Get new ID
        $newId = count($posts) > 0 ? end($posts)['id'] + 1 : 1;

        $newPost = [
            "id" => $newId,
            "title" => $title,
            "content" => $content,
            "author" => $_SESSION['user']['username'],
            "created_at" => date("Y-m-d H:i")
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

<?php include 'includes/header.php'; ?>

<h2>Add New Post</h2>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; ?>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="5" cols="40"><?= htmlspecialchars($content) ?></textarea><br><br>

    <input type="submit" value="Post">
</form>

</body>
</html>
