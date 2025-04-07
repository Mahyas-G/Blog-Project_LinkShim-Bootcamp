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

$anyPost = flase;

foreach ($posts as $index => $post) {
    if ($post['id'] === $postId) {
        if ($post['author'] !== $_SESSION['user']['username']) {
            die("You can only edit your own posts.");
        }
        $anyPost = true;
        $postIndex = $index;
        break;
    }
}

if (!$anyPost) {
    die("Post not found.");
}

$title = $posts[$postIndex]['title'];
$content = $posts[$postIndex]['content'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    if (empty($errors)) {
        $posts[$postIndex]['title'] = $title;
        $posts[$postIndex]['content'] = $content;

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
</head>
<body>
<div class="container">
<?php include 'includes/header.php'; ?>

<h2>Edit Post</h2>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; ?>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="5" cols="40"><?= htmlspecialchars($content) ?></textarea><br><br>

    <input type="submit" value="Update">
</form>
</div>
</body>
</html>