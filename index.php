<?php
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
<h2>All Blog Posts</h2>

<?php
if (empty($posts)) {
    echo "<p>No posts yet.</p>";
} else {
    foreach (array_reverse($posts) as $post) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
        echo "<p><small>By " . htmlspecialchars($post['author']) . " on " . $post['created_at'] . "</small></p>";
        echo "<p><a href='view_post.php?id={$post['id']}'>Read More</a></p>";
        if ($post['author'] == $_SESSION['user']['username']) {
            echo "<p><a href='edit_post.php?id={$post['author']}'>Edit</a> </p>";
        }
        echo "</div><hr>";
    }
}
?>
</div>
</body>
</html>
