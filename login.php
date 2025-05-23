<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$users = [];
if (file_exists("data/users.json")) {
    $users = json_decode(file_get_contents("data/users.json"), true);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    } else {
        $found = false;
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $_SESSION['user'] = $user;
                header("Location: index.php");
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
<div class="container">
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
</div>
</body>
</html>
