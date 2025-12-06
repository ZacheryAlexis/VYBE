<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('cart_db.php');
require_once('security.php');

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=cart">Back</a>');
}

$itemID = $_POST['itemID'] ?? null;
$action = $_POST['action'] ?? null;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($itemID && $action) {
    switch ($action) {
        case 'increase':
            if (isset($_SESSION['cart'][$itemID])) {
                $_SESSION['cart'][$itemID]['quantity']++;
            }
            break;
            
        case 'decrease':
            if (isset($_SESSION['cart'][$itemID])) {
                $_SESSION['cart'][$itemID]['quantity']--;
                if ($_SESSION['cart'][$itemID]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$itemID]);
                }
            }
            break;
            
        case 'remove':
            if (isset($_SESSION['cart'][$itemID])) {
                unset($_SESSION['cart'][$itemID]);
            }
            break;
    }
}

// Save to database if user is logged in
if (!empty($_SESSION['user_id'])) {
    saveCartToDB($_SESSION['user_id'], $_SESSION['cart']);
}

// Redirect back to cart
header('Location: index.php?content=cart');
exit();
?>
