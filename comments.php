<?php
session_start();

require_once 'includes/comment_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You need to login to post comments.";
        header("Location: login.php");
        exit;
    }

    $postId = $_POST['post_id'];
    $content = $_POST['content'];

    if (!empty($content)) {
        addComment($postId, $content);
    }

    header("Location: view_post.php?id=$postId");
    exit;
}
?>