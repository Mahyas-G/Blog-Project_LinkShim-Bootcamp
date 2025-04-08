<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    exit("Invalid request.");
}

$id = (int)$_GET['id'];

// بارگذاری پست‌ها
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$users = file_exists("data/users.json") ? json_decode(file_get_contents("data/users.json"), true) : [];

foreach ($posts as &$post) {
    if ($post['id'] === $id) {
        $post['score'] = ($post['score'] ?? 0) + 1;

        // افزایش امتیاز نویسنده‌ی پست
        foreach ($users as &$user) {
            if ($user['username'] === $post['author']) {
                $user['score'] = ($user['score'] ?? 0) + 1;
                break;
            }
        }

        break;
    }
}

// ذخیره تغییرات
file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));

header("Location: index.php");
exit;
