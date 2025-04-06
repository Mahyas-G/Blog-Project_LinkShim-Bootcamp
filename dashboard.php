<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];

$posts = [];
if (file_exists("data/posts.json")) {
    $posts = json_decode(file_get_contents("data/posts.json"), true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">

<?php include 'includes/header.php'; ?>

<h2>Your Posts</h2>
<a href="add_post.php" style="text-align: center;">+ Add New Post</a>
<br>
<br>

<?php
$userPosts = array_filter($posts, fn($post) => $post['author'] === $username);

if (empty($userPosts)) {
    echo "<p>You haven't written any posts yet.</p>";
} else {
    foreach ($userPosts as $post) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
        echo "<p><small>Created at: " . $post['created_at'] . "</small></p>";
        echo "<p><a href='edit_post.php?id={$post['id']}'>Edit</a> | <a href='delete_post.php?id={$post['id']}'>Delete</a></p>";
        echo "</div><hr>";
    }
}
?>
</div>
</body>
</html>
