<?php
require_once('database.php');
session_start();

$email = filter_var($_POST['emailAddress'], FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$first = htmlspecialchars($_POST['firstName'] ?? '');
$last = htmlspecialchars($_POST['lastName'] ?? '');

if (!$email || !$password) {
    echo "<h3>Please provide a valid email and password.</h3>";
    echo "<a href=\"index.php?content=newuser\">Back</a>";
    exit();
}

$db = getDB();
// check existing
$stmt = $db->prepare("SELECT userID FROM users WHERE emailAddress = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<h3>An account with that email already exists. Try logging in.</h3>";
    echo "<a href=\"index.php?content=user_login\">Login</a>";
    $stmt->close();
    $db->close();
    exit();
}
$stmt->close();

// hash password (store SHA-256 to match other SQL examples)
$hashed = hash('sha256', $password);

$insert = $db->prepare("INSERT INTO users (emailAddress, password, firstName, lastName) VALUES (?, ?, ?, ?)");
$insert->bind_param('ssss', $email, $hashed, $first, $last);
$ok = $insert->execute();
if ($ok) {
    $userID = $insert->insert_id;
    $_SESSION['user_id'] = $userID;
    $_SESSION['user_name'] = trim($first . ' ' . $last) ?: $email;
    // after creating account, send user to quiz
    header('Location: index.php?content=quiz');
    exit();
} else {
    echo "<h3>Unable to create account right now. Please try again later.</h3>";
}
$insert->close();
$db->close();
?>