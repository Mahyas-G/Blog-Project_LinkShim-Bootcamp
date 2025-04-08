<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];

$found = false;

foreach ($posts as $index => $post) {
    if ($post['id'] === $postId) {
        if ($post['author'] !== $_SESSION['user']['username']) {
            die("You can only delete your own posts.");
        }
        
        // Delete associated image file if it exists
        if (!empty($post['image']) && file_exists($post['image'])) {
            unlink($post['image']);
        }
        
        unset($posts[$index]);
        $posts = array_values($posts);
        file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
        header("Location: dashboard.php");
        exit;
    }
}

die("Post not found.");
