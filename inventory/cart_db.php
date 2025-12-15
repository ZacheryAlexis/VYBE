<?php
// cart_db.php - Helper functions for persistent cart storage

require_once('database.php');

/**
 * Save cart to database for logged-in user
 */
function saveCartToDB($userID, $cart) {
    if (empty($userID)) return false;
    
    $db = getDB();
    $cartJson = json_encode(array_values($cart)); // Convert to indexed array for JSON
    $stmt = $db->prepare("UPDATE users SET cart = ? WHERE userID = ?");
    $stmt->bind_param('si', $cartJson, $userID);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $success;
}

/**
 * Load cart from database for logged-in user
 */
function loadCartFromDB($userID) {
    if (empty($userID)) return array();
    
    $db = getDB();
    $stmt = $db->prepare("SELECT cart FROM users WHERE userID = ?");
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    
    if ($row && !empty($row['cart'])) {
        $cartArray = json_decode($row['cart'], true);
        // Convert back to associative array with itemID as key
        $cart = array();
        foreach ($cartArray as $item) {
            if (isset($item['itemID'])) {
                // Build a composite key when variantID exists
                $key = $item['itemID'];
                if (isset($item['variantID']) && $item['variantID']) {
                    $key = $key . ':v' . $item['variantID'];
                }
                $cart[$key] = $item;
            }
        }
        return $cart;
    }
    
    return array();
}

/**
 * Clear cart from database
 */
function clearCartInDB($userID) {
    if (empty($userID)) return false;
    
    $db = getDB();
    $emptyCart = json_encode(array());
    $stmt = $db->prepare("UPDATE users SET cart = ? WHERE userID = ?");
    $stmt->bind_param('si', $emptyCart, $userID);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $success;
}

/**
 * Sync session cart with database on login
 */
function syncCartOnLogin($userID) {
    // Load cart from database
    $dbCart = loadCartFromDB($userID);
    
    // Merge with session cart if exists
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $itemID => $item) {
            if (isset($dbCart[$itemID])) {
                // Item exists in both - add quantities
                $dbCart[$itemID]['quantity'] += $item['quantity'];
            } else {
                // Item only in session - add to db cart
                $dbCart[$itemID] = $item;
            }
        }
    }
    
    // Save merged cart back to session and database
    $_SESSION['cart'] = $dbCart;
    saveCartToDB($userID, $dbCart);
}
?>
