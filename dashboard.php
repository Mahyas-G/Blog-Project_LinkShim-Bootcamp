<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$ratings = file_exists("data/ratings.json") ? json_decode(file_get_contents("data/ratings.json"), true) : [];

// توابع مربوط به rating
function getPostRatings($ratings, $postId)
{
    return $ratings[$postId] ?? [];
}

function calculateAverageRating($postRatings)
{
    if (empty($postRatings)) {
        return 0;
    }
    return round(array_sum($postRatings) / count($postRatings), 1);
}

function getUserRating($postRatings, $username)
{
    return $postRatings[$username] ?? null;
}

function displayStarRating($rating)
{
    $stars = str_repeat("★", floor($rating));
    if (fmod($rating, 1) >= 0.5) {
        $stars .= "½";
    }
    $stars .= str_repeat("☆", 10 - ceil($rating));
    return $stars;
}

// پردازش امتیازدهی
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
    <style>
        .post-image {
            max-width: 100%;
            max-height: 250px;
            margin: 10px 0;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .star-rating {
            font-size: 1.2em;
            color: gold;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>My Posts</h2>
    <a href="add_post.php" class="btn btn-add-post">+ Add New Post</a>

    <?php
    if (empty($posts)) {
        echo "<p>No posts available.</p>";
    } else {
        foreach ($posts as $post) {
            if ($post['author'] !== $username) {
                continue;
            }

            $postId = $post['id'];
            $postRatings = getPostRatings($ratings, $postId);
            $averageRating = calculateAverageRating($postRatings);
            $userRating = getUserRating($postRatings, $username);

            echo "<div class='post'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<p><small>Created at: " . $post['created_at'] . " by " . htmlspecialchars($post['author']) . "</small></p>";

            if (!empty($post['image'])) {
                echo "<img src='" . htmlspecialchars($post['image']) . "' class='post-image' alt='Post image'>";
            }

            echo "<div class='rating-info'>";
            echo "<p>Average Rating: <span class='star-rating'>" . displayStarRating($averageRating) . "</span> ($averageRating/10)</p>";

            if ($userRating !== null) {
                echo "<p>Your Rating: <span class='star-rating'>" . displayStarRating($userRating) . "</span> ($userRating/10)</p>";
            } else {
                echo "<form method='POST' class='rating-form'>";
                echo "<input type='hidden' name='post_id' value='$postId'>";
                echo "<label for='rating'>Rate this post (1–10):</label> ";
                echo "<input type='number' name='rating' min='1' max='10' required>";
                echo " <button type='submit' class='btn'>Submit</button>";
                echo "</form>";
            }
            echo "</div>"; // end .rating-info

            echo "<div class='post-actions'>";
            echo "<a href='view_post.php?id=$postId' class='btn'>View</a>";
            echo "<a href='edit_post.php?id=$postId' class='btn'>Edit</a>";
            echo "<a href='delete_post.php?id=$postId' class='btn' onclick='return confirm(\"Are you sure you want to delete this post?\")'>Delete</a>";
            echo "</div>";

            echo "</div>"; // end .post
        }
    }
    ?>
</div>

</body>
</html>