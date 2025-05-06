<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';

// Fetch posts from database
$username = $_SESSION['user']['username'];
$posts = [];
$ratings = []; // Initialize ratings array

try {
    
    $sql = "SELECT * FROM posts WHERE author = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    
    if (file_exists("data/ratings.json")) {
        $ratings = json_decode(file_get_contents("data/ratings.json"), true) ?? [];
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

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
        .post {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .post-image {
            max-width: 100%;
            max-height: 250px;
            margin: 10px 0;
            border-radius: 8px;
            object-fit: cover;
        }
        .star-rating {
            font-size: 1.2em;
            color: gold;
            margin: 5px 0;
        }
        .post-actions a {
            margin-right: 10px;
            color: #4CAF50;
            text-decoration: none;
        }
        .post-actions a:hover {
            text-decoration: underline;
        }
        .rating-form {
            margin: 10px 0;
        }
        .btn-rate {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .add-post-btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>My Posts</h2>
        <a href="add_post.php" class="add-post-btn">+ Add New Post</a>

        <?php if (empty($posts)): ?>
            <p>No posts available. Create your first post!</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class='post'>
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <p><small>Posted on: <?= $post['created_at'] ?> by <?= htmlspecialchars($post['author']) ?></small></p>

                    <?php if (!empty($post['image'])): ?>
                        <img src="<?= htmlspecialchars($post['image']) ?>" class="post-image" alt="Post image">
                    <?php endif; ?>

                    <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?><?= strlen($post['content']) > 200 ? '...' : '' ?></p>

                    <!-- Rating Section -->
                    <div class='rating-info'>
                        <?php
                        $postId = $post['id'];
                        $postRatings = getPostRatings($ratings, $postId);
                        $averageRating = calculateAverageRating($postRatings);
                        $userRating = getUserRating($postRatings, $username);
                        ?>
                        
                        <p>Average Rating: <span class='star-rating'><?= displayStarRating($averageRating) ?></span> (<?= $averageRating ?>/10)</p>

                        <?php if ($userRating !== null): ?>
                            <p>Your Rating: <span class='star-rating'><?= displayStarRating($userRating) ?></span> (<?= $userRating ?>/10)</p>
                        <?php else: ?>
                            <form method='POST' class='rating-form'>
                                <input type='hidden' name='post_id' value='<?= $postId ?>'>
                                <label for='rating'>Rate this post (1-10):</label>
                                <input type='number' name='rating' min='1' max='10' required>
                                <button type='submit' class='btn-rate'>Submit Rating</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class='post-actions'>
                        <a href='view_post.php?id=<?= $post['id'] ?>'>View</a>
                        <a href='edit_post.php?id=<?= $post['id'] ?>'>Edit</a>
                        <a href='delete_post.php?id=<?= $post['id'] ?>' onclick='return confirm("Are you sure you want to delete this post?")'>Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
