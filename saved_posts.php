<?php
session_start();

// بارگذاری پست‌های ذخیره‌شده از فایل saved_posts.json
$savedPostsFile = "data/saved_posts.json";
$savedPosts = file_exists($savedPostsFile) ? json_decode(file_get_contents($savedPostsFile), true) : [];

// بررسی اینکه کاربر وارد شده است یا خیر
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user']['username'];
    $userSavedPosts = $savedPosts[$username] ?? [];
} else {
    $userSavedPosts = [];
}

$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Posts</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Saved Posts</h2>

    <?php if (empty($userSavedPosts)): ?>
        <p>No saved posts available.</p>
    <?php else: ?>
        <div class="posts-list">
            <?php foreach ($posts as $post): ?>
                <?php if (in_array($post['id'], $userSavedPosts)): ?>
                    <div class="post-item">
                        <h3><a href="view_post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                        <small>By <?= htmlspecialchars($post['author']) ?> on <?= htmlspecialchars($post['created_at']) ?></small>
                        <?php if (!empty($post['image'])): ?>
                            <div class="post-thumbnail">
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                            </div>
                        <?php endif; ?>
                        <div class="post-preview">
                            <?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
