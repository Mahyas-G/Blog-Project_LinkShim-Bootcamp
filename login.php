<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include 'includes/db.php';

// اتصال به پایگاه داده
$servername = "localhost";
$username = "root";
$password = "44413138";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// متغیر برای ذخیره داده‌ها
$users = [];

// کوئری برای گرفتن تمامی کاربران از پایگاه داده
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// بررسی نتایج و اضافه کردن داده‌ها به آرایه $users
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "هیچ کاربری پیدا نشد.";
}

// بستن اتصال
$conn->close();


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
