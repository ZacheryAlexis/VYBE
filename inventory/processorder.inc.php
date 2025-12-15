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
$paymentMethod = $_POST['paymentMethod'] ?? 'card';

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
    'orderDate' => date('F j, Y g:i A'),
    'paymentMethod' => $paymentMethod
);

// If PayPal, redirect to PayPal payment page (simulated)
if ($paymentMethod === 'paypal') {
    // Store pending order data
    $_SESSION['pending_order'] = array(
        'fullName' => $fullName,
        'email' => $email,
        'address' => $address,
        'city' => $city,
        'state' => $state,
        'zip' => $zip,
        'phone' => $phone
    );
    
    // In a real implementation, this would redirect to PayPal API
    // For this academic project, we'll simulate it
    header('Location: index.php?content=paypal');
    exit();
}

// Process credit card payment (simulated)
// Save order to database
require_once('database.php');
$db = getDB();

// Insert order
$userID = $_SESSION['user_id'] ?? null;
$orderDate = date('Y-m-d H:i:s');
$shippingAddressFull = "$address, $city, $state $zip";

$stmt = $db->prepare("
    INSERT INTO orders (orderNumber, userID, emailAddress, fullName, shippingAddress, phoneNumber, 
                       subtotal, tax, total, paymentMethod, orderDate, orderStatus)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");
 $stmt->bind_param('sissssdddss', $orderNumber, $userID, $email, $fullName, $shippingAddressFull, 
                   $phone, $total, $tax, $grandTotal, $paymentMethod, $orderDate);
$stmt->execute();
$orderID = $db->insert_id;
$stmt->close();

// Insert order items
$itemStmt = $db->prepare("
    INSERT INTO order_items (orderID, itemID, itemName, quantity, price)
    VALUES (?, ?, ?, ?, ?)
");

require_once('item.php');
foreach ($cart as $item) {
    $orderItemName = $item['itemName'];
    if (!empty($item['variantID'])) {
        $v = Item::getVariantByID(intval($item['variantID']));
        if ($v && !empty($v['sizeLabel'])) {
            $orderItemName = $v['sizeLabel'] . ' — ' . $orderItemName;
        }
    }
    $itemStmt->bind_param('iisid', $orderID, $item['itemID'], $orderItemName, 
                         $item['quantity'], $item['listPrice']);
    $itemStmt->execute();
}
$itemStmt->close();
$db->close();

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