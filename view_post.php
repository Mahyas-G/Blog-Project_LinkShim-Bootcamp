<?php
// view_post.php
session_start(); // Retaining session start as per your request

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the post from the database
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if (!$post) {
        echo "<p>Post not found.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $rating = $_POST['rating'];
    $user = $_SESSION['user']['username']; // Assuming the session holds the username

    // Save the rating to the database
    $sql = "INSERT INTO ratings (post_id, username, rating) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE rating = VALUES(rating)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $id, $user, $rating);
    $stmt->execute();
    $stmt->close();
}

// Fetch ratings and comments
$userRating = null;
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user']['username'];
    $sql = "SELECT rating FROM ratings WHERE post_id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) {
        $userRating = $row['rating'];
    }
    $stmt->close();
}

$conn->close();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Post</title>
    </head>
<body>
<div class="post">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    <p><strong>Author:</strong> <?= htmlspecialchars($post['author']) ?></p>
    <p><em>Created at:</em> <?= $post['created_at'] ?></p>
    <p><em>Last updated:</em> <?= $post['updated_at'] ?></p>

    <div class="ratings">
        <?php
        if (isset($_SESSION['user'])) {
            if ($userRating !== null) {
                echo "<p><strong>Your Rating:</strong> <span class='star-rating'>" . htmlspecialchars($userRating) . "</span> ($userRating/10)</p>";
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

    <!-- The rest of your unchanged code -->
    <div class="comments-section">
        <h3>Comments</h3>
<?php
require_once 'includes/comment_functions.php';

include 'includes/db.php';

$comments = getComments($post['id']);
if (empty($comments)) {
    echo "<p>No comments yet.</p>";
} else {
    foreach ($comments as $comment) {
        echo "<div class='comment'>";
        echo "<p class='comment-author'>" . htmlspecialchars($comment['author']) .
            " <span class='comment-date'>(" . $comment['created_at'] . ")</span></p>";
        echo "<p class='comment-content'>" . htmlspecialchars($comment['content']) . "</p>";
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
        <!-- Post actions code remains unchanged -->
    </div>
</div>
</body>
</html>