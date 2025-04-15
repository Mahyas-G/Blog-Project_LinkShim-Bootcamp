<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$to = $_GET['to'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if ($message === '') {
        $errors[] = "پیام نمی‌تواند خالی باشد.";
    } else {
        $messages = file_exists("data/messages.json") ? json_decode(file_get_contents("data/messages.json"), true) : [];

        $messages[] = [
            'from' => $_SESSION['user']['username'],
            'to' => $to,
            'message' => $message,
            'timestamp' => date("Y-m-d H:i")
        ];

        file_put_contents("data/messages.json", json_encode($messages, JSON_PRETTY_PRINT));
        header("Location: inbox.php");
        exit;
    }
}
?>

<form method="POST">
    <h2>ارسال پیام به <?= htmlspecialchars($to) ?></h2>
    <textarea name="message" placeholder="متن پیام..."></textarea>
    <button type="submit">ارسال</button>
</form>
