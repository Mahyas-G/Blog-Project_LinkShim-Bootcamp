<?php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

foreach ($posts as $post) {
    if ($post['id'] === $postId) {
        // Mark post as "read" using a cookie
        setcookie("read_post_$postId", "1", time() + (86400 * 30), "/"); // 30 days
        $foundPost = $post;
        break;
    }
}

if (!isset($foundPost)) {
    die("Post not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($foundPost['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<h2><?= htmlspecialchars($foundPost['title']) ?></h2>
<p><small>By <?= htmlspecialchars($foundPost['author']) ?> on <?= $foundPost['created_at'] ?></small></p>
<p><?= nl2br(htmlspecialchars($foundPost['content'])) ?></p>

<p><a href="index.php">← Back to all posts</a></p>
</div>
</body>
</html>
