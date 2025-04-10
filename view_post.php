<?php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

foreach ($posts as $post) {
    if ($post['id'] === $postId) {
        setcookie("read_post_$postId", "1", time() + (86400 * 30), "/"); 
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
    <?php include 'includes/header.php'; ?>
    <h2><?= htmlspecialchars($foundPost['title']) ?></h2>
    <p><small>By <?= htmlspecialchars($foundPost['author']) ?> on <?= $foundPost['created_at'] ?></small></p>
    
    <?php if (!empty($foundPost['image'])): ?>
        <div class="post-image">
            <img src="<?= htmlspecialchars($foundPost['image']) ?>" alt="Post image" style="max-width: 100%; height: auto;">
        </div>
    <?php endif; ?>
    
    <p><?= nl2br(htmlspecialchars($foundPost['content'])) ?></p>

    <p><a href="index.php">← Back to all posts</a></p>
</div>
</body>
</html>
