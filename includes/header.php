<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>


<header class="navbar">
    <div class="navbar-left">
        <img src="assets/logo.png" alt="Logo" class="logo">
        <span class="brand-name">The Blog</span>
    </div>
    <div class="navbar-container">
        <!-- فرم جستجو -->
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search posts or users..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">🔍</button>
        </form>
    </div>
    <!-- نوار ناوبری راست -->
    <div class="navbar-right">
        <?php if (isset($_SESSION['user'])): ?>
            <a href="dashboard.php" class="btn btn-dashboard">Dashboard</a>
            <a href="index.php" class="btn btn-home">Home</a>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-login">Login</a>
        <?php endif; ?>
    </div>

</header>



