<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$ratings = file_exists("data/ratings.json") ? json_decode(file_get_contents("data/ratings.json"), true) : [];

//توابع مربوط به rating
function getPostRatings($ratings, $postId) {
    return $ratings[$postId] ?? [];
}

//function calculateAverageRating($postRatings) {
//    if (empty($postRatings)) return 0;
//    return round(array_sum($postRatings) / count($postRatings), 1);
//}

function getUserRating($postRatings, $username) {
    return $postRatings[$username] ?? null;
}

function displayStarRating($rating) {
    $stars = str_repeat("★", floor($rating));
    if (fmod($rating, 1) >= 0.5) {
        $stars .= "½";
    }
    $stars .= str_repeat("☆", 10 - ceil($rating));
    return $stars;
}

//پیدا کردن پست
foreach ($posts as $post) {
    if ($post['id'] === $postId) {
        setcookie("read_post_$postId", "1", time() + (86400 * 30), "/"); 
        $foundPost = $post;
        break;
    }
}

if (!isset($foundPost)) {
    die("Post not found.");
}

//پردازش ارسال rating
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user'], $_POST['rating'])) {
    $username = $_SESSION['user']['username'];
    $rating = (int) $_POST['rating'];
    
    if ($rating >= 1 && $rating <= 10) {
        $ratings[$postId][$username] = $rating;
        file_put_contents("data/ratings.json", json_encode($ratings, JSON_PRETTY_PRINT));
    }
    header("Location: view_post.php?id=$postId");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($foundPost['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    
    <h2><?= htmlspecialchars($foundPost['title']) ?></h2>
    <p><small>By <?= htmlspecialchars($foundPost['author']) ?> on <?= $foundPost['created_at'] ?></small></p>
    
    <?php if (!empty($foundPost['image'])): ?>
        <div class="post-image">
            <img src="<?= htmlspecialchars($foundPost['image']) ?>" alt="Post image" style="max-width: 100%; height: auto;">
        </div>
    <?php endif; ?>
    
    <p><?= nl2br(htmlspecialchars($foundPost['content'])) ?></p>

    <hr>

    <?php
    //نمایش rating
    $postRatings = getPostRatings($ratings, $postId);
    //$averageRating = calculateAverageRating($postRatings);
    //echo "<p><strong>Average Rating:</strong> <span class='star-rating'>" . displayStarRating($averageRating) . "</span> ($averageRating / 10)</p>";

    if (isset($_SESSION['user'])) {
        $username = $_SESSION['user']['username'];
        $userRating = getUserRating($postRatings, $username);

        if ($userRating !== null) {
            echo "<p><strong>Your Rating:</strong> <span class='star-rating'>" . displayStarRating($userRating) . "</span> ($userRating / 10)</p>";
        } else {
            echo "<form method='POST'>";
            echo "<label for='rating'>Rate this post (1–10):</label> ";
            echo "<input type='number' name='rating' min='1' max='10' required> ";
            echo "<button type='submit' class='btn'>Submit</button>";
            echo "</form>";
        }
    } else {
        echo "<p><a href='login.php'>Log in</a> to rate this post.</p>";
    }
    ?>

    <p><a href="index.php">← Back to all posts</a></p>
</div>
</body>
</html>
