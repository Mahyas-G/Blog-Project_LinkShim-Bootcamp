<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$messages = file_exists("data/messages.json") ? json_decode(file_get_contents("data/messages.json"), true) : [];

$inbox = array_filter($messages, function ($msg) use ($username) {
    return $msg['to'] === $username;
});
?>

<h2>📥 Inbox of massages</h2>
<?php foreach ($inbox as $msg): ?>
    <div class="message-box">
        <strong>from:</strong> <?= htmlspecialchars($msg['from']) ?><br>
        <strong>Time:</strong> <?= htmlspecialchars($msg['timestamp']) ?><br>
        <p><?= htmlspecialchars($msg['message']) ?></p>
        <a href="send_message.php?to=<?= urlencode($msg['from']) ?>">🔁 Answer</a>
    </div>
<?php endforeach; ?>
