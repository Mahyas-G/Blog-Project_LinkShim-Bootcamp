<?php
session_start();
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

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
</head>
<style>
    .post-image-container {
        width: 100%;
        overflow: hidden;
        margin: 10px 0;
        border-radius: 10px;
    }

    .post-image {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .post-image:hover {
        transform: scale(1.02);
    }
</style>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>All Blog Posts</h2>

    <?php
    if (empty($posts)) {
        echo "<p>No posts yet.</p>";
    } else {
        foreach (array_reverse($posts) as $post) {
            echo "<div class='post'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            /* here we should fix the image weight and height.
               because the css style not loaded to this file i don't know  but fix it to gather👍*/
            if (!empty($post['image'])) {
                echo "<div class='post-image-container'>";
                echo "<img class='post-image' src='" . htmlspecialchars($post['image']) . "' alt='" . htmlspecialchars($post['title']) . "'>";
                echo "</div>";
            }

            $preview = strip_tags($post['content']);
            $preview = substr($preview, 0, 100);
            if (strlen($post['content']) > 100) {
                $preview .= '...';
            }
            echo "<p class='post-preview'>" . htmlspecialchars($preview) . "</p>";

            echo "<p><small>By " . htmlspecialchars($post['author']) . " on " . $post['created_at'] . "</small></p>";

            echo "<p><a href='view_post.php?id={$post['id']}'>Read More</a></p>";

            if (isset($_SESSION['user']) && $post['author'] === $_SESSION['user']['username']) {
                echo "<p><a href='edit_post.php?id={$post['id']}'>Edit</a></p>";
            }

            echo "</div><hr>";
        }
    }
    ?>
</div>

<!-- اسکریپت افکت کرکره‌ای -->
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
