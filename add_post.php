<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === '' || $content === '') {
        $errors[] = "Both title and content are required.";
    }

    if (empty($errors)) {
        $posts = [];
        if (file_exists("data/posts.json")) {
            $posts = json_decode(file_get_contents("data/posts.json"), true);
        }

        $newId = count($posts) > 0 ? end($posts)['id'] + 1 : 1;

        $newPost = [
            "id" => $newId,
            "title" => $title,
            "content" => $content,
            "author" => $_SESSION['user']['username'],
            "created_at" => date("Y-m-d H:i")
        ];

        $posts[] = $newPost;

        file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
        header("Location: dashboard.php");
        exit;
    }
}
?>
