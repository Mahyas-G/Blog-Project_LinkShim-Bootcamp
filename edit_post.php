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

$anyPost = flase;

foreach ($posts as $index => $post) {
    if ($post['id'] === $postId) {
        if ($post['author'] !== $_SESSION['user']['username']) {
            die("You can only edit your own posts.");
        }
        $anyPost = true;
        $postIndex = $index;
        break;
    }
}

if (!$anyPost) {
    die("Post not found.");
}
