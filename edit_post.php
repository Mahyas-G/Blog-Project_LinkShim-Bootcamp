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
