<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('cart_db.php');
require_once('security.php');

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php">Back</a>');
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$itemID = $_POST['itemID'] ?? null;
$quantity = intval($_POST['quantity'] ?? 1);
$fromWishlist = isset($_POST['from_wishlist']);

if ($itemID && is_numeric($itemID)) {
    $item = Item::findItem($itemID);
    
    if ($item) {
        // Check stock availability
        if ($item->stockQuantity <= 0) {
            if ($fromWishlist) {
                $_SESSION['cart_message'] = 'Sorry, ' . htmlspecialchars($item->itemName) . ' is out of stock.';
                header('Location: index.php?content=wishlist');
                exit();
            }
            echo "<div class='panel' style='border: 2px solid #f56565;'>";
            echo "<h2 style='color: #f56565;'>Out of Stock</h2>";
            echo "<p><strong>" . htmlspecialchars($item->itemName) . "</strong> is currently out of stock.</p>";
            echo "<p><a class='accent-link' href='index.php?content=listitems'>Continue Shopping</a></p>";
            echo "</div>";
            return;
        }
        
        // Check if requested quantity exceeds stock
        $requestedQty = $quantity;
        if (isset($_SESSION['cart'][$itemID])) {
            $requestedQty += $_SESSION['cart'][$itemID]['quantity'];
        }
        
        if ($requestedQty > $item->stockQuantity) {
            $quantity = $item->stockQuantity - ($_SESSION['cart'][$itemID]['quantity'] ?? 0);
            if ($quantity <= 0) {
                if ($fromWishlist) {
                    $_SESSION['cart_message'] = 'Maximum available quantity already in cart.';
                    header('Location: index.php?content=wishlist');
                    exit();
                }
                echo "<div class='panel' style='border: 2px solid #ed8936;'>";
                echo "<h2 style='color: #ed8936;'>Limited Stock</h2>";
                echo "<p>You already have the maximum available quantity in your cart.</p>";
                echo "<p><a class='accent-link' href='index.php?content=cart'>View Cart</a></p>";
                echo "</div>";
                return;
            }
        }
        
        // Check if item already in cart
        if (isset($_SESSION['cart'][$itemID])) {
            $_SESSION['cart'][$itemID]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$itemID] = array(
                'itemID' => $item->itemID,
                'itemName' => $item->itemName,
                'listPrice' => $item->listPrice,
                'quantity' => $quantity
            );
        }
        
        // Save to database if user is logged in
        if (!empty($_SESSION['user_id'])) {
            saveCartToDB($_SESSION['user_id'], $_SESSION['cart']);
        }
        
        // Redirect if from wishlist
        if ($fromWishlist) {
            $_SESSION['cart_message'] = htmlspecialchars($item->itemName) . ' added to cart!';
            header('Location: index.php?content=wishlist');
            exit();
        }
        
        echo "<div class='panel' style='border: 2px solid var(--vybe-orange);'>";
        echo "<h2 style='color: var(--vybe-orange);'>✓ Added to Cart</h2>";
        echo "<p><strong>" . htmlspecialchars($item->itemName) . "</strong> has been added to your cart.";
        if ($requestedQty > $item->stockQuantity) {
            echo " <span style='color: #ed8936;'>(Only " . $quantity . " available)</span>";
        }
        echo "</p>";
        echo "<p><a class='accent-link' href='index.php?content=cart'>View Cart</a> &nbsp;•&nbsp; <a class='accent-link' href='index.php?content=listitems'>Continue Shopping</a></p>";
        echo "</div>";
    } else {
        echo "<h3>Item not found.</h3>";
    }
} else {
    echo "<h3>Invalid item.</h3>";
}
?>
