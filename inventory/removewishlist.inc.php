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
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=wishlist">Back</a>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['wishlistID'])) {
    header('Location: index.php?content=wishlist');
    exit();
}

require_once('database.php');

$wishlistID = intval($_POST['wishlistID']);
$userID = $_SESSION['user_id'];

$db = getDB();
$stmt = $db->prepare("DELETE FROM wishlist WHERE wishlistID = ? AND userID = ?");
$stmt->bind_param('ii', $wishlistID, $userID);

if ($stmt->execute()) {
    $_SESSION['wishlist_message'] = 'Removed from wishlist.';
} else {
    $_SESSION['wishlist_message'] = 'Failed to remove item.';
}

$stmt->close();
$db->close();

header('Location: index.php?content=wishlist');
exit();
?>
