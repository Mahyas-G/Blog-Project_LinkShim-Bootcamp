<?php
session_start();

// Load posts
$postsFile = "data/posts.json";
$posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

// Load users (to get author scores)
$usersFile = "data/users.json";
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

// Function to get author's total score
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
            $score = 0;
            if (!empty($post['likes'])) {
                foreach ($post['likes'] as $vote) {
                    if ($vote === 'like') $score++;
                    elseif ($vote === 'dislike') $score--;
                }
            }

            echo "<div class='post'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<p><small>By " . htmlspecialchars($post['author']) . " (User Score: " . getUserScore($post['author'], $users) . ") on " . $post['created_at'] . "</small></p>";
            echo "<p><a href='view_post.php?id={$post['id']}'>Read More</a></p>";

            echo "<p class='score-box'>⭐ Score: $score</p>";

            if (isset($_SESSION['user'])) {
                echo "<form method='GET' action='like_post.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='id' value='{$post['id']}'>";
                echo "<input type='hidden' name='action' value='like'>";
                echo "<button type='submit' class='like-btn'>👍 Like</button>";
                echo "</form>";

                echo "<form method='GET' action='like_post.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='id' value='{$post['id']}'>";
                echo "<input type='hidden' name='action' value='dislike'>";
                echo "<button type='submit' class='dislike-btn'>👎 Dislike</button>";
                echo "</form>";
            }

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
