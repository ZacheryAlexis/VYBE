<?php
require_once('database.php');
session_start();

$email = filter_var($_POST['emailAddress'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo "<h3>Please enter valid credentials.</h3>";
    echo "<a href=\"index.php?content=user_login\">Back</a>";
    exit();
}

$db = getDB();
$hashed = hash('sha256', $password);
$stmt = $db->prepare("SELECT userID, firstName, lastName, quizResults FROM users WHERE emailAddress = ? AND password = ?");
$stmt->bind_param('ss', $email, $hashed);
$stmt->execute();
$stmt->bind_result($userID, $first, $last, $quizResults);
$fetched = $stmt->fetch();
$stmt->close();
$db->close();

if ($fetched) {
    $_SESSION['user_id'] = $userID;
    $_SESSION['user_name'] = trim($first . ' ' . $last) ?: $email;
    // if quizResults is empty/null, redirect to quiz
    if (empty($quizResults)) {
        header('Location: index.php?content=quiz');
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    echo "<h3>Login failed. Check email/password.</h3>";
    echo "<a href=\"index.php?content=user_login\">Try again</a>";
}
?>