
<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$ratings = file_exists("data/ratings.json") ? json_decode(file_get_contents("data/ratings.json"), true) : [];

// Rating functions
function getPostRatings($ratings, $postId) {
    return $ratings[$postId] ?? [];
}

function calculateAverageRating($postRatings) {
    if (empty($postRatings)) return 0;
    return round(array_sum($postRatings) / count($postRatings), 1);
}

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

// Find the post
$foundPost = null;
foreach ($posts as $post) {
    if ($post['id'] === $postId) {
        setcookie("read_post_$postId", "1", time() + (86400 * 30), "/"); 
        $foundPost = $post;
        break;
    }
}

if (!$foundPost) {
    die("Post not found.");
}

// Process rating submission
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
    <style>
        .post-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .post-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .post-content {
            line-height: 1.6;
            font-size: 1.1em;
        }
        .rating-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .star-rating {
            font-size: 1.2em;
            color: gold;
            margin: 5px 0;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container post-container">
    <h2><?= htmlspecialchars($foundPost['title']) ?></h2>
    <p><small>By <?= htmlspecialchars($foundPost['author']) ?> on <?= $foundPost['created_at'] ?></small></p>

    <?php if (!empty($foundPost['image'])): ?>
        <div class="post-image-container">
            <img src="<?= htmlspecialchars($foundPost['image']) ?>" alt="Post image" class="post-image">
        </div>
    <?php endif; ?>
    
    <div class="post-content">
        <?= nl2br(htmlspecialchars($foundPost['content'])) ?>
    </div>

    <div class="rating-section">
        <?php
        $postRatings = getPostRatings($ratings, $postId);
        $averageRating = calculateAverageRating($postRatings);
        echo "<p><strong>Average Rating:</strong> <span class='star-rating'>" . displayStarRating($averageRating) . "</span> ($averageRating/10)</p>";

        if (isset($_SESSION['user'])) {
            $username = $_SESSION['user']['username'];
            $userRating = getUserRating($postRatings, $username);

            if ($userRating !== null) {
                echo "<p><strong>Your Rating:</strong> <span class='star-rating'>" . displayStarRating($userRating) . "</span> ($userRating/10)</p>";
            } else {
                echo "<form method='POST'>";
                echo "<label for='rating'>Rate this post (1–10):</label> ";
                echo "<input type='number' name='rating' min='1' max='10' required> ";
                echo "<button type='submit' class='btn-rate'>Submit Rating</button>";
                echo "</form>";
            }
        } else {
            echo "<p><a href='login.php'>Log in</a> to rate this post.</p>";
        }
        ?>
    </div>

    <div class="post-content">
        <?php if (isset($_SESSION['user']) && $foundPost['author'] === $_SESSION['user']['username']): ?>
            <a href="edit_post.php?id=<?= $postId ?>" >Edit Post</a>
        <?php endif; ?>
    </div>

    <a href="index.php"> ← Back to all posts</a>

</div>
</body>
</html>
