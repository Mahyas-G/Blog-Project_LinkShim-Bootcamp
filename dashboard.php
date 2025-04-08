<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$ratings = file_exists("data/ratings.json") ? json_decode(file_get_contents("data/ratings.json"), true) : [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_id'], $_POST['rating'])) {
    $postId = $_POST['post_id'];
    $rating = (int) $_POST['rating'];
    
    if ($rating >= 1 && $rating <= 10) {
        $ratings[$postId][$username] = $rating;
        file_put_contents("data/ratings.json", json_encode($ratings, JSON_PRETTY_PRINT));
    }
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">

<?php include 'includes/header.php'; ?>

<h2>All Posts</h2>
<a href="add_post.php" style="text-align: center;">+ Add New Post</a>
<br><br>

<?php
if (empty($posts)) {
    echo "<p>No posts available.</p>";
} else {
    foreach ($posts as $post) {
        $postId = $post['id'];
        $postRatings = $ratings[$postId] ?? [];
        $averageRating = empty($postRatings) ? "No ratings yet" : round(array_sum($postRatings) / count($postRatings), 1) . "/10";
        $userRating = $postRatings[$username] ?? null;
        
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
        echo "<p><small>Created at: " . $post['created_at'] . " by " . htmlspecialchars($post['author']) . "</small></p>";
        echo "<p>Average Rating: $averageRating</p>";
        if ($userRating !== null) {
            echo "<p>Your Rating: $userRating</p>";
        } else {
            echo "<form method='POST'>";
            echo "<input type='hidden' name='post_id' value='$postId'>";
            echo "<label for='rating'>Rate this post (1-10): </label>";
            echo "<input type='number' name='rating' min='1' max='10' required>";
            echo "<button type='submit'>Submit</button>";
            echo "</form>";
        }
        echo "</div><hr>";
    }
}
?>
</div>
</body>
</html>
