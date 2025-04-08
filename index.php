<?php
session_start();

// دریافت پست‌ها
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

// دریافت کاربران (برای نمایش امتیاز نویسنده)
$users = file_exists("data/users.json") ? json_decode(file_get_contents("data/users.json"), true) : [];

// تابع برای دریافت امتیاز کاربر
function getUserScore($username, $users) {
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user['score'] ?? 0;
        }
    }
    return 0;
}
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
            echo "<p><small>By " . htmlspecialchars($post['author']) . " (User Score: " . getUserScore($post['author'], $users) . ") on " . $post['created_at'] . "</small></p>";
            echo "<p><a href='view_post.php?id={$post['id']}'>Read More</a></p>";

            // امتیاز پست
            echo "<p class='score-box'>⭐ Score: " . ($post['score'] ?? 0) . "</p>";

            // دکمه لایک اگر کاربر وارد شده باشد
            if (isset($_SESSION['user'])) {
                echo "<form method='GET' action='like_post.php'>";
                echo "<input type='hidden' name='id' value='{$post['id']}'>";
                echo "<button type='submit' class='like-btn'>👍 Like</button>";
                echo "</form>";
            }

            // دکمه ویرایش
            if (isset($_SESSION['user']) && $post['author'] === $_SESSION['user']['username']) {
                echo "<p><a href='edit_post.php?id={$post['id']}'>Edit</a></p>";
            }

            echo "</div><hr>";
        }
    }
    ?>
</div>
</body>
</html>
