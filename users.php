<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$users = file_exists("data/users.json") ? json_decode(file_get_contents("data/users.json"), true) : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-list {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 64, 0.2);
            max-width: 600px;
            margin: 30px auto;
        }

        .user-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f5faff;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid #d0e6ff;
        }

        .user-card span {
            font-weight: 500;
            font-size: 16px;
            color: #003366;
        }

        .msg-btn {
            padding: 6px 14px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .msg-btn:hover {
            background: #0056b3;
        }

        h2 {
            text-align: center;
            color: #003366;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="user-list">
    <h2>Users</h2>

    <?php foreach ($users as $user): ?>
        <?php if ($user['username'] !== $_SESSION['user']['username']): ?>
            <div class="user-card">
                <span><?= htmlspecialchars($user['username']) ?></span>
                <a href="send_message.php?to=<?= urlencode($user['username']) ?>">
                    <button class="msg-btn">Send Message</button>
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

</body>
</html>
