<?php
function getComments($postId) {
    if (!file_exists('data/comments.json')) return [];
    $comments = json_decode(file_get_contents('data/comments.json'), true) ?: [];
    return array_filter($comments, fn($c) => $c['post_id'] == $postId);
}

function addComment($postId, $content) {
    $comments = json_decode(file_get_contents('data/comments.json'), true) ?: [];

    $newComment = [
        'id' => count($comments) + 1,
        'post_id' => $postId,
        'author' => $_SESSION['user']['username'],
        'content' => htmlspecialchars($content),
        'created_at' => date('Y-m-d H:i:s')
    ];

    $comments[] = $newComment;
    file_put_contents('data/comments.json', json_encode($comments, JSON_PRETTY_PRINT));
    return $newComment;
}
?>
