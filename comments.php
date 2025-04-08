<?php
require_once 'includes/config.php';
require_once 'includes/comment_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $content = $_POST['content'];
    $author = $_POST['author'] ?? 'Guest';
    
    if (!empty($content)) {
        addComment($postId, $content, $author);
    }
    
    header("Location: view_post.php?id=$postId");
    exit;
}
//check
?>
