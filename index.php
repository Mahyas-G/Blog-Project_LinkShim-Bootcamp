<?php
session_start();

// دریافت پست‌ها
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

// دریافت کاربران برای نمایش امتیاز
$users = file_exists("data/users.json") ? json_decode(file_get_contents("data/users.json"), true) : [];

// تابع برای دریافت امتیاز نویسنده
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
    <style>
        .post-image-thumbnail {
            max-width: 200px;
            max-height: 150px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
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
            
            // نمایش تصویر پست اگر وجود داشته باشد
            if (!empty($post['image'])) {
                echo "<img src='" . htmlspecialchars($post['image']) . "' class='post-image-thumbnail' alt='Post thumbnail'>";
            }
            
            echo "<p><a href='view_post.php?id={$post['id']}'>Read More</a></p>";

            // نمایش امتیاز پست
            $score = $post['score'] ?? 0;
            echo "<p class='score-box'>⭐️ Score: $score</p>";

            // لایک / دیسلایک اگر کاربر لاگین کرده
            if (isset($_SESSION['user'])) {
                $currentUser = $_SESSION['user']['username'];
                $userVote = $post['likes'][$currentUser] ?? null;

                echo "<form method='GET' action='like_post.php' style='display:inline;'>";
                echo "<input type='hidden' name='id' value='{$post['id']}'>";
                echo "<input type='hidden' name='action' value='like'>";
                echo "<button type='submit' class='like-btn' " . ($userVote === 'like' ? 'disabled' : '') . ">👍 Like</button>";
                echo "</form>";

                echo "<form method='GET' action='like_post.php' style='display:inline;'>";
                echo "<input type='hidden' name='id' value='{$post['id']}'>";
                echo "<input type='hidden' name='action' value='dislike'>";
                echo "<button type='submit' class='dislike-btn' " . ($userVote === 'dislike' ? 'disabled' : '') . ">👎 Dislike</button>";
                echo "</form>";
            }

            // دکمه ویرایش اگر کاربر صاحب پست باشد
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
