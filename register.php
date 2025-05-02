<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    }

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Username already taken.";
    }

    if (empty($errors)) {
        // Hash the password before saving it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database securely
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $hashedPassword);
        $stmt->execute();

        // Set session for the user
        $_SESSION['user'] = ['username' => $username];
        header("Location: dashboard.php");
        exit;
    }
}

$conn->close();
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

    <input type="submit" value="Register">
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
