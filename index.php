<?php
session_start();
require_once 'includes/db.php';

$posts = [];
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
}

$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

if (!empty($searchQuery)) {
    $posts = array_filter($posts, function($post) use ($searchQuery) {
        return strpos(strtolower($post['title']), $searchQuery) !== false ||
               strpos(strtolower($post['author']), $searchQuery) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .post {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .post-thumbnail img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 6px;
            margin: 10px 0;
            object-fit: cover;
        }
        .post-preview {
            color: #555;
            line-height: 1.6;
            margin: 10px 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        hr {
            border: 0;
            height: 1px;
            background: #eee;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>All Blog Posts</h2>

    <?php if (empty($posts)): ?>
        <p>No posts yet. Be the first to <a href="add_post.php">create a post</a>!</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class='post'>
                <h3><?= htmlspecialchars($post['title']) ?></h3>

                <?php if (!empty($post['image'])): ?>
                    <div class='post-thumbnail'>
                        <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                    </div>
                <?php endif; ?>

                <?php
                $preview = strip_tags($post['content']);
                $preview = substr($preview, 0, 200);
                if (strlen($post['content']) > 200) {
                    $preview .= '...';
                }
                ?>
                <p class='post-preview'><?= htmlspecialchars($preview) ?></p>

                <p><small>By <?= htmlspecialchars($post['author']) ?> on <?= $post['created_at'] ?></small></p>

                <p><a href='view_post.php?id=<?= $post['id'] ?>'>Read More</a></p>

                <?php if (isset($_SESSION['user']) && $post['author'] === $_SESSION['user']['username']): ?>
                    <p><a href='edit_post.php?id=<?= $post['id'] ?>'>Edit</a></p>
                <?php endif; ?>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    window.addEventListener("scroll", function() {
        const navbar = document.querySelector(".navbar");
        if (window.scrollY > 20) {
            navbar.style.boxShadow = "0 4px 12px rgba(0,0,0,0.2)";
        } else {
            navbar.style.boxShadow = "0 4px 12px rgba(0,0,64,0.1)";
        }
    });
</script>

</body>
</html>
<?php $conn->close(); ?>