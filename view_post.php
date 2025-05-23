<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$postId = (int)$_GET['id'];
$posts = file_exists("data/posts.json") ? json_decode(file_get_contents("data/posts.json"), true) : [];
$ratings = file_exists("data/ratings.json") ? json_decode(file_get_contents("data/ratings.json"), true) : [];

// توابع امتیازدهی
function getPostRatings($ratings, $postId) {
    return $ratings[$postId] ?? [];
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

// توابع ذخیره پست
function getSavedPosts($userId) {
    $savedFile = "data/saved_posts.json";
    if (!file_exists($savedFile)) return [];
    $allSaved = json_decode(file_get_contents($savedFile), true);
    return $allSaved[$userId] ?? [];
}

function toggleSavePost($userId, $postId) {
    $savedFile = "data/saved_posts.json";
    $allSaved = file_exists($savedFile) ? json_decode(file_get_contents($savedFile), true) : [];

    if (!isset($allSaved[$userId])) {
        $allSaved[$userId] = [];
    }

    if (in_array($postId, $allSaved[$userId])) {
        // حذف از ذخیره‌شده‌ها
        $allSaved[$userId] = array_values(array_diff($allSaved[$userId], [$postId]));
        $action = "removed";
    } else {
        // اضافه کردن
        $allSaved[$userId][] = $postId;
        $action = "saved";
    }

    file_put_contents($savedFile, json_encode($allSaved, JSON_PRETTY_PRINT));
    return $action;
}

// یافتن پست
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

// پردازش ارسال امتیاز
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

// پردازش ذخیره/حذف پست
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['toggle_save']) && isset($_SESSION['user'])) {
    $username = $_SESSION['user']['username'];
    toggleSavePost($username, $postId);
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
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="post">
        <h2><?= htmlspecialchars($foundPost['title']) ?></h2>
        <small>By <?= htmlspecialchars($foundPost['author']) ?> on <?= $foundPost['created_at'] ?></small>

        <?php if (!empty($foundPost['image'])): ?>
            <div class="post-thumbnail">
                <img src="<?= htmlspecialchars($foundPost['image']) ?>" alt="Post image">
            </div>
        <?php endif; ?>

        <div class="post-preview">
            <?= nl2br(htmlspecialchars($foundPost['content'])) ?>
        </div>

        <div class="rating-section">
            <h3>Ratings</h3>
            <?php
            $postRatings = getPostRatings($ratings, $postId);

            if (isset($_SESSION['user'])) {
                $username = $_SESSION['user']['username'];
                $userRating = getUserRating($postRatings, $username);

                if ($userRating !== null) {
                    echo "<p><strong>Your Rating:</strong> <span class='star-rating'>" . displayStarRating($userRating) . "</span> ($userRating/10)</p>";
                } else {
                    echo "<form method='POST'>";
                    echo "<label for='rating'>Rate this post (1-10):</label>";
                    echo "<input type='number' name='rating' min='1' max='10' required>";
                    echo "<button type='submit' class='btn-rate'>Submit Rating</button>";
                    echo "</form>";
                }
            } else {
                echo "<p><a href='login.php'>Log in</a> to rate this post.</p>";
            }
            ?>
        </div>

        <div class="comments-section">
            <h3>Comments</h3>
            <?php
            require_once 'includes/comment_functions.php';

            $comments = getComments($post['id']);
            if (empty($comments)) {
                echo "<p>No comments yet.</p>";
            } else {
                foreach ($comments as $comment) {
                    echo "<div class='comment'>";
                    echo "<p class='comment-author'>" . htmlspecialchars($comment['author']) . 
                        " <span class='comment-date'>(" . $comment['created_at'] . ")</span></p>";
                    echo "<p class='comment-content'>" . $comment['content'] . "</p>";
                    echo "</div>";
                }
            }

            if (isset($_SESSION['user'])) {
                echo "<form method='post' action='comments.php' class='comment-form'>";
                echo "<input type='hidden' name='post_id' value='" . $post['id'] . "'>";
                echo "<textarea name='content' placeholder='Write your comment...' required></textarea>";
                echo "<button type='submit' class='btn-rate'>Post Comment</button>";
                echo "</form>";
            } else {
                echo "<div class='alert'>Please <a href='login.php'>login</a> to post a comment.</div>";
            }
            ?>
        </div>

        <div class="post-actions">
        <?php if (isset($_SESSION['user']) && $foundPost['author'] === $_SESSION['user']['username']): ?>
            <a href="edit_post.php?id=<?= $postId ?>" class="edit-link">Edit Post</a>
        <?php endif; ?>

        <?php
        if (isset($_SESSION['user'])) {
            $username = $_SESSION['user']['username'];
            $savedPosts = getSavedPosts($username);
            $isSaved = in_array($postId, $savedPosts);
            echo "<form method='POST' style='display:inline; margin-top:10px;'>";
            echo "<input type='hidden' name='toggle_save' value='1'>";
            echo "<button type='submit' class='btn-rate'>" . ($isSaved ? "Unsave Post" : "Save Post") . "</button>";
            echo "</form>";
        
            // دکمه رفتن به صفحه پست‌های ذخیره‌شده
            echo "<br/>";
            echo "<a href='saved_posts.php' class='btn-rate' style='margin-top:10px; display:inline-block;'>View Saved Posts</a>";
        }
        ?>

    <br/>
    <a href="index.php" class="back-link">← Back to all posts</a>
</div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
