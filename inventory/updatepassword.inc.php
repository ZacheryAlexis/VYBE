<?php
require_once('database.php');
require_once('security.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?content=user_login');
    exit();
}

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=changepassword">Back</a>');
}

$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validate inputs
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo "<h3>All fields are required.</h3>";
    echo "<a href=\"index.php?content=changepassword\">Back</a>";
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo "<h3>New passwords do not match.</h3>";
    echo "<a href=\"index.php?content=changepassword\">Back</a>";
    exit();
}

if (strlen($newPassword) < 8) {
    echo "<h3>New password must be at least 8 characters long.</h3>";
    echo "<a href=\"index.php?content=changepassword\">Back</a>";
    exit();
}

// Get current password hash
$db = getDB();
$stmt = $db->prepare("SELECT password FROM users WHERE userID = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($storedHash);
$fetched = $stmt->fetch();
$stmt->close();

if (!$fetched) {
    $db->close();
    echo "<h3>User not found.</h3>";
    echo "<a href=\"index.php?content=changepassword\">Back</a>";
    exit();
}

// Verify current password
if (!verify_password($currentPassword, $storedHash)) {
    $db->close();
    echo "<h3>Current password is incorrect.</h3>";
    echo "<a href=\"index.php?content=changepassword\">Back</a>";
    exit();
}

// Hash new password and update
$newHash = hash_password($newPassword);
$stmt = $db->prepare("UPDATE users SET password = ? WHERE userID = ?");
$stmt->bind_param('si', $newHash, $_SESSION['user_id']);
$result = $stmt->execute();
$stmt->close();
$db->close();

if ($result) {
    echo "<div class=\"panel\" style=\"max-width: 600px; margin: 0 auto;\">";
    echo "<h2 style=\"color: var(--vybe-orange); margin-top: 0;\">✓ Password Updated</h2>";
    echo "<p style=\"color: var(--vybe-text);\">Your password has been changed successfully.</p>";
    echo "<p><a class=\"accent-link\" href=\"index.php?content=profile\">← Back to Profile</a></p>";
    echo "</div>";
} else {
    echo "<div class=\"panel\" style=\"max-width: 600px; margin: 0 auto;\">";
    echo "<h2 style=\"color: var(--vybe-orange); margin-top: 0;\">⚠ Update Failed</h2>";
    echo "<p style=\"color: var(--vybe-text);\">Unable to update your password. Please try again.</p>";
    echo "<p><a class=\"accent-link\" href=\"index.php?content=changepassword\">← Try Again</a></p>";
    echo "</div>";
}
?>
