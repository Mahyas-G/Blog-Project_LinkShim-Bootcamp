
<?php
session_start();
if (isset($_SESSION['user'])) {
    header("location:dashboard.php");
    exit();
}
$user = [];
if (file_exists("data/user.json")) {
    $user = json_decode(file_get_contents("data/user.json"), true);
}
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    if (empty($username) or empty($password)){
        $errors[] = "Username or Password is empty";
    }else{
        foreach ($user as $user) {
            if ($user["username"] == $username and $user["password"] == $password){
                $_SESSION["user"] = $user;
                header("location:dashboard.php");
                exit();
            }
        }
        $errors[] = "Username or Password is invalid";
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













