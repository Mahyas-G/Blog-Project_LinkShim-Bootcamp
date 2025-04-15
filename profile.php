<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>پروفایل من</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #f2f2f2;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .profile-box {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .profile-box h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .profile-info {
            margin-top: 20px;
            text-align: left;
            direction: rtl;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .profile-actions {
            margin-top: 30px;
        }

        .profile-actions a {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            margin: 0 10px;
            transition: background 0.3s;
        }

        .profile-actions a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="profile-box">
    <h2>👤My profile</h2>
    <div class="profile-info">
        <p><strong>User name:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'Not registered') ?></p>

    </div>

    <div class="profile-actions">
        <a href="inbox.php">📩 massages</a>
        <a href="dashboard.php">🏠 dashboard</a>
    </div>
</div>

</body>
</html>
