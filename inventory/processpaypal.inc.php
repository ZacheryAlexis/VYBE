<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if there's a pending order
if (empty($_SESSION['pending_order']) || empty($_SESSION['cart'])) {
    header('Location: index.php?content=cart');
    exit();
}

$cart = $_SESSION['cart'];
$total = 0;

foreach ($cart as $item) {
    $total += $item['listPrice'] * $item['quantity'];
}
$tax = $total * 0.07;
$grandTotal = $total + $tax;

// Simulate PayPal payment processing
$paypalEmail = htmlspecialchars($_POST['paypal_email'] ?? '');

// Generate order number
$orderNumber = 'VYBE-' . strtoupper(substr(md5(time() . rand()), 0, 8));

// Get pending order data
$pendingOrder = $_SESSION['pending_order'];

// Store completed order in session
$_SESSION['last_order'] = array(
    'orderNumber' => $orderNumber,
    'items' => $cart,
    'subtotal' => $total,
    'tax' => $tax,
    'total' => $grandTotal,
    'shippingName' => $pendingOrder['fullName'],
    'shippingEmail' => $pendingOrder['email'],
    'shippingAddress' => "{$pendingOrder['address']}, {$pendingOrder['city']}, {$pendingOrder['state']} {$pendingOrder['zip']}",
    'orderDate' => date('F j, Y g:i A'),
    'paymentMethod' => 'paypal',
    'paypalEmail' => $paypalEmail
);

// Save order to database
require_once('database.php');
$db = getDB();

$userID = $_SESSION['user_id'] ?? null;
$orderDate = date('Y-m-d H:i:s');
$shippingAddressFull = "{$pendingOrder['address']}, {$pendingOrder['city']}, {$pendingOrder['state']} {$pendingOrder['zip']}";

$stmt = $db->prepare("
    INSERT INTO orders (orderNumber, userID, emailAddress, fullName, shippingAddress, phoneNumber, 
                       subtotal, tax, total, paymentMethod, paypalEmail, orderDate, orderStatus)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'paypal', ?, ?, 'pending')
");
$stmt->bind_param('sissssdddss', $orderNumber, $userID, $pendingOrder['email'], $pendingOrder['fullName'], 
                  $shippingAddressFull, $pendingOrder['phone'], $total, $tax, $grandTotal, 
                  $paypalEmail, $orderDate);
$stmt->execute();
$orderID = $db->insert_id;
$stmt->close();

// Insert order items
$itemStmt = $db->prepare("
    INSERT INTO order_items (orderID, itemID, itemName, quantity, price)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($cart as $item) {
    $itemStmt->bind_param('iisid', $orderID, $item['itemID'], $item['itemName'], 
                         $item['quantity'], $item['listPrice']);
    $itemStmt->execute();
}
$itemStmt->close();
$db->close();

// Clear the cart
$_SESSION['cart'] = array();

// Clear pending order
unset($_SESSION['pending_order']);

// Clear cart in database if user is logged in
if (!empty($_SESSION['user_id'])) {
    require_once('cart_db.php');
    clearCartInDB($_SESSION['user_id']);
}

// Redirect to confirmation page
header('Location: index.php?content=orderconfirmation');
exit();
?>
