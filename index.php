<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="container">
    <h1>Welcome to our team blog</h1>
    <p>Share your thoughts and discover others' stories.</p>

    <?php if (!isset($_SESSION['user'])): ?>
        <a href="login.php" class="btn btn-login">Login</a>
        <a href="register.php" class="btn btn-signup">Sign Up</a>
    <?php else: ?>
        <a href="dashboard.php" class="btn btn-login">Dashboard</a>
        <a href="logout.php" class="btn btn-signup">Logout</a>
    <?php endif; ?>

    <div class="footer">
        &copy; <?= date('Y') ?> OUR Blog Project
    </div>
</div>

</body>
</html>
