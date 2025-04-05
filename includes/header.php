<?php
session_start();
?>

<nav>
    <?php if (isset($_SESSION['user'])): ?>
        Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>
<hr>
