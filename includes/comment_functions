<?php
function getComments($postId) {
    if (!file_exists('comments.json')) return [];
    $comments = json_decode(file_get_contents('comments.json'), true) ?: [];
    return array_filter($comments, fn($c) => $c['post_id'] == $postId);
}

function addComment($postId, $content, $author = 'Guest') {
    $comments = json_decode(file_get_contents('comments.json'), true) ?: [];
    
    $newComment = [
        'id' => count($comments) + 1,
        'post_id' => $postId,
        'author' => isset($_SESSION['user']) ? $_SESSION['user']['username'] : $author,
        'content' => htmlspecialchars($content),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $comments[] = $newComment;
    file_put_contents('comments.json', json_encode($comments, JSON_PRETTY_PRINT));
    return $newComment;
}
?>
