<?php
session_start();

// If already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

// Load users from JSON file
$users = [];
if (file_exists("data/users.json")) {
    $users = json_decode(file_get_contents("data/users.json"), true);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Simple validation
    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    } else {
        $found = false;
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $_SESSION['user'] = $user; // Save full user data in session
                header("Location: dashboard.php");
                exit;
            }
        }
        $errors[] = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Login</h2>

<?php
foreach ($errors as $error) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <input type="submit" value="Login">
</form>

<p>Don’t have an account? <a href="register.php">Register here</a></p>
</body>
</html>
