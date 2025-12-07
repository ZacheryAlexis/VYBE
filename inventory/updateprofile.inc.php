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
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=profile">Back</a>');
}

$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');

if (empty($firstName) || empty($lastName)) {
    echo "<h3>Please provide both first and last name.</h3>";
    echo "<a href=\"index.php?content=profile\">Back</a>";
    exit();
}

$db = getDB();
$stmt = $db->prepare("UPDATE users SET firstName = ?, lastName = ? WHERE userID = ?");
$stmt->bind_param('ssi', $firstName, $lastName, $_SESSION['user_id']);
$result = $stmt->execute();
$stmt->close();
$db->close();

if ($result) {
    // Update session
    $_SESSION['user_name'] = trim($firstName . ' ' . $lastName);
    
    echo "<div class=\"panel\" style=\"max-width: 600px; margin: 0 auto;\">";
    echo "<h2 style=\"color: var(--vybe-orange); margin-top: 0;\">✓ Profile Updated</h2>";
    echo "<p style=\"color: var(--vybe-text);\">Your profile information has been updated successfully.</p>";
    echo "<p><a class=\"accent-link\" href=\"index.php?content=profile\">← Back to Profile</a></p>";
    echo "</div>";
} else {
    echo "<div class=\"panel\" style=\"max-width: 600px; margin: 0 auto;\">";
    echo "<h2 style=\"color: var(--vybe-orange); margin-top: 0;\">⚠ Update Failed</h2>";
    echo "<p style=\"color: var(--vybe-text);\">Unable to update your profile. Please try again.</p>";
    echo "<p><a class=\"accent-link\" href=\"index.php?content=profile\">← Back to Profile</a></p>";
    echo "</div>";
}
?>
