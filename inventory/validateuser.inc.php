<?php
require_once('database.php');

// only start session if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = filter_var($_POST['emailAddress'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo "<h3>Please enter valid credentials.</h3>";
    echo "<a href=\"index.php?content=user_login\">Back</a>";
    exit();
}

$db = getDB();
if (!$db || (isset($db->connect_errno) && $db->connect_errno)) {
    // log server-side, show friendly message to user
    error_log('DB connect error: ' . ($db->connect_error ?? 'unknown'));
    echo "<h3>Server error. Please try again later.</h3>";
    exit();
}

// Current approach: comparing SHA256 hash (keeps your current DB layout).
// Note: see section B for recommended password approach.
$hashed = hash('sha256', $password);

$stmt = $db->prepare("SELECT userID, firstName, lastName, quizResults FROM users WHERE emailAddress = ? AND password = ?");
if (!$stmt) {
    error_log('Prepare failed: ' . $db->error);
    echo "<h3>Login failed due to a server error.</h3>";
    exit();
}

$stmt->bind_param('ss', $email, $hashed);
if (!$stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    echo "<h3>Login failed due to a server error.</h3>";
    $stmt->close();
    exit();
}

$stmt->bind_result($userID, $first, $last, $quizResults);
$fetched = $stmt->fetch();
$stmt->close();
$db->close();

if ($fetched) {
    $_SESSION['user_id'] = $userID;
    $_SESSION['user_name'] = trim($first . ' ' . $last) ?: $email;
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
