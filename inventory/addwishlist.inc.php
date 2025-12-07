<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?content=user_login');
    exit();
}

require_once('security.php');

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php">Back</a>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['itemID'])) {
    header('Location: index.php');
    exit();
}

require_once('database.php');

$itemID = intval($_POST['itemID']);
$userID = $_SESSION['user_id'];

$db = getDB();

// Check if item already in wishlist
$checkStmt = $db->prepare("SELECT wishlistID FROM wishlist WHERE userID = ? AND itemID = ?");
$checkStmt->bind_param('ii', $userID, $itemID);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    $db->close();
    $_SESSION['wishlist_message'] = 'Item already in your wishlist!';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit();
}
$checkStmt->close();

// Add to wishlist
$stmt = $db->prepare("INSERT INTO wishlist (userID, itemID) VALUES (?, ?)");
$stmt->bind_param('ii', $userID, $itemID);

if ($stmt->execute()) {
    $_SESSION['wishlist_message'] = 'Added to wishlist! ♥';
} else {
    $_SESSION['wishlist_message'] = 'Failed to add to wishlist.';
}

$stmt->close();
$db->close();

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();
?>
