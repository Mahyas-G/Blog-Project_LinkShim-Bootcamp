<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!$post_id || !in_array($action, ['like', 'dislike'])) {
    exit("Invalid request.");
}

// بارگذاری داده‌ها
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$users = file_exists("data/users.json") ? json_decode(file_get_contents("data/users.json"), true) : [];

// پردازش پست و رای‌دهی
foreach ($posts as &$post) {
    if ($post['id'] === $post_id) {

        // مقداردهی اولیه
        if (!isset($post['likes'])) $post['likes'] = [];
        if (!isset($post['score'])) $post['score'] = 0;

        $previousVote = $post['likes'][$username] ?? null;

        if ($previousVote === $action) {
            // اگر قبلاً همون رای رو داده بود
            break;
        }

        // اگر قبلاً رأی متفاوت داده بود، باید امتیاز اصلاح بشه
        if ($previousVote === 'like') {
            $post['score']--;
        } elseif ($previousVote === 'dislike') {
            $post['score']++;
        }

        // ثبت رأی جدید
        if ($action === 'like') {
            $post['score']++;
        } elseif ($action === 'dislike') {
            $post['score']--;
        }

        $post['likes'][$username] = $action;

        // همچنین امتیاز نویسنده رو آپدیت کنیم
        foreach ($users as &$user) {
            if ($user['username'] === $post['author']) {
                if (!isset($user['score'])) $user['score'] = 0;

                if ($previousVote === 'like') $user['score']--;
                if ($previousVote === 'dislike') $user['score']++;

                if ($action === 'like') $user['score']++;
                if ($action === 'dislike') $user['score']--;
            }
        }

        break;
    }
}

// ذخیره فایل‌ها
file_put_contents("data/posts.json", json_encode($posts, JSON_PRETTY_PRINT));
file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));

header("Location: index.php");
exit;
