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
$variantID = isset($_POST['variantID']) && $_POST['variantID'] !== '' ? intval($_POST['variantID']) : NULL;
$userID = $_SESSION['user_id'];

$db = getDB();

$checkStmt = null;
// Handle NULL variantID separately to avoid ambiguous comparisons with prepared params
$hasVariantColumn = false;
try {
    $colRes = $db->query("SHOW COLUMNS FROM wishlist LIKE 'variantID'");
    if ($colRes && $colRes->num_rows > 0) $hasVariantColumn = true;
} catch (Exception $e) {
    $hasVariantColumn = false;
}

// Check if item already in wishlist (handle schemas with/without variantID)
if ($hasVariantColumn) {
    if (is_null($variantID)) {
        $checkStmt = $db->prepare("SELECT wishlistID FROM wishlist WHERE userID = ? AND itemID = ? AND variantID IS NULL");
        $checkStmt->bind_param('ii', $userID, $itemID);
    } else {
        $checkStmt = $db->prepare("SELECT wishlistID FROM wishlist WHERE userID = ? AND itemID = ? AND variantID = ?");
        $checkStmt->bind_param('iii', $userID, $itemID, $variantID);
    }
} else {
    $checkStmt = $db->prepare("SELECT wishlistID FROM wishlist WHERE userID = ? AND itemID = ?");
    $checkStmt->bind_param('ii', $userID, $itemID);
}
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

// Add to wishlist (handle NULL variant separately)
if ($hasVariantColumn) {
    if (is_null($variantID)) {
        $stmt = $db->prepare("INSERT INTO wishlist (userID, itemID) VALUES (?, ?)");
        $stmt->bind_param('ii', $userID, $itemID);
    } else {
        $stmt = $db->prepare("INSERT INTO wishlist (userID, itemID, variantID) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $userID, $itemID, $variantID);
    }
} else {
    // Old schema: ignore variantID and insert only itemID
    $stmt = $db->prepare("INSERT INTO wishlist (userID, itemID) VALUES (?, ?)");
    $stmt->bind_param('ii', $userID, $itemID);
}

if ($stmt->execute()) {
    $_SESSION['wishlist_message'] = 'Added to wishlist! ♥';
} else {
    $_SESSION['wishlist_message'] = 'Failed to add to wishlist.';
}

$stmt->close();
$db->close();

// Redirect back. Prefer original Referer, but if it doesn't include an itemID
// (some browsers or proxies strip query strings), redirect explicitly to the
// item's display page to avoid "You did not select a valid itemID" errors.
$rawRef = $_SERVER['HTTP_REFERER'] ?? '';
$safeRef = preg_replace('/[\r\n].*/', '', $rawRef);
if ($safeRef && strpos($safeRef, 'itemID=') !== false) {
    $redirect = $safeRef;
} else {
    $redirect = 'index.php?content=displayitem&itemID=' . urlencode($itemID);
}
header('Location: ' . $redirect);
exit();
?>
