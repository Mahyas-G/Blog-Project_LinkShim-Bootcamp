<?php
session_start();

if (!isset($_SESSION['user'])) {
    // اگر کاربر لاگین نکرده، اجازه ندیم
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$postId = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$postId || !in_array($action, ['like', 'dislike'])) {
    header("Location: index.php");
    exit;
}

// بارگیری پست‌ها
$postsFile = "data/posts.json";
$posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

// پیدا کردن پست مورد نظر و اعمال رأی
foreach ($posts as &$post) {
    if ($post['id'] == $postId) {
        if (!isset($post['likes'])) {
            $post['likes'] = [];
        }

        // رأی قبلی کاربر
        $previousVote = $post['likes'][$username] ?? null;

        // اگر رأی قبلی با رأی جدید یکی بود، کاری نکن
        if ($previousVote === $action) {
            break;
        }

        // اعمال رأی جدید
        $post['likes'][$username] = $action;

        // حذف فیلد score اگر وجود داشت (ما از لایک‌ها مستقیم محاسبه می‌کنیم)
        if (isset($post['score'])) {
            unset($post['score']);
        }

        break;
    }
}

// ذخیره پست‌ها
file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));

// بازگشت به صفحه قبل
header("Location: index.php");
exit;
