<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<nav class="navbar">
    <div class="navbar-left">
        <img src="assets/logo.png" alt="Logo" class="logo">
        <span class="brand-name">The Blog</span>
    </div>
    <div class="navbar-right">
        <?php if (isset($_SESSION['user'])): ?>
            <span class="welcome">👋 Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
