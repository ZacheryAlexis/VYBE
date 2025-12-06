<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate payment processing
require_once('security.php');

// Validate CSRF token
if (!validate_csrf_token()) {
    die('<h3>Security error. Please try again.</h3><a href="index.php?content=checkout">Back</a>');
}

$cart = $_SESSION['cart'] ?? array();
$total = 0;

if (empty($cart)) {
    header('Location: index.php?content=cart');
    exit();
}

// Get form data
$fullName = htmlspecialchars($_POST['fullName'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$address = htmlspecialchars($_POST['address'] ?? '');
$city = htmlspecialchars($_POST['city'] ?? '');
$state = htmlspecialchars($_POST['state'] ?? '');
$zip = htmlspecialchars($_POST['zip'] ?? '');
$phone = htmlspecialchars($_POST['phone'] ?? '');

// Calculate totals
foreach ($cart as $item) {
    $total += $item['listPrice'] * $item['quantity'];
}
$tax = $total * 0.07;
$grandTotal = $total + $tax;

// Generate order number
$orderNumber = 'VYBE-' . strtoupper(substr(md5(time() . rand()), 0, 8));

// Store order details in session for confirmation
$_SESSION['last_order'] = array(
    'orderNumber' => $orderNumber,
    'items' => $cart,
    'subtotal' => $total,
    'tax' => $tax,
    'total' => $grandTotal,
    'shippingName' => $fullName,
    'shippingEmail' => $email,
    'shippingAddress' => "$address, $city, $state $zip",
    'orderDate' => date('F j, Y g:i A')
);

// Clear the cart
$_SESSION['cart'] = array();

// Clear cart in database if user is logged in
if (!empty($_SESSION['user_id'])) {
    require_once('cart_db.php');
    clearCartInDB($_SESSION['user_id']);
}

// Redirect to confirmation page
header('Location: index.php?content=orderconfirmation');
exit();
?>
