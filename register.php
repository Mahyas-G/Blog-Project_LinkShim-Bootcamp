<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
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
    }
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $errors[] = "Username already taken.";
            break;
        }
    }
    if (empty($errors)) {
        $newUser = [
            "id" => count($users) + 1,
            "username" => $username,
            "password" => $password 
        ];
        $users[] = $newUser;
        file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<h2>Register</h2>
<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username"><br><br>
    <label>Password:</label><br>
    <input type="password" name="password"><br><br>
    <input type="submit" value="Register">
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
