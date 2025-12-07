<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_GET['orderID'])) {
    header('Location: index.php?content=orderhistory');
    exit();
}

require_once('database.php');

$orderID = intval($_GET['orderID']);
$userID = $_SESSION['user_id'];

$db = getDB();

// Get order details
$stmt = $db->prepare("
    SELECT orderNumber, orderDate, fullName, shippingAddress, phoneNumber, 
           subtotal, tax, total, paymentMethod, paypalEmail, orderStatus, emailAddress
    FROM orders
    WHERE orderID = ? AND userID = ?
");
$stmt->bind_param('ii', $orderID, $userID);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    $db->close();
    echo "<p>Order not found.</p>";
    return;
}

// Get order items
$stmt = $db->prepare("
    SELECT itemName, quantity, price
    FROM order_items
    WHERE orderID = ?
");
$stmt->bind_param('i', $orderID);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$db->close();
?>

<style>
.order-details-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px;
}
.back-link {
    color: var(--vybe-text);
    text-decoration: none;
    margin-bottom: 20px;
    display: inline-block;
    transition: color 0.3s ease;
}
.back-link:hover {
    color: var(--vybe-orange);
}
.order-details-header {
    background: var(--vybe-card);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid rgba(199,185,255,0.2);
}
.order-details-header h1 {
    color: var(--vybe-orange);
    margin: 0 0 5px 0;
    font-size: 1.8rem;
}
.order-details-subtitle {
    color: var(--vybe-muted);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.details-section {
    background: var(--vybe-card);
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 12px;
    border: 1px solid rgba(199,185,255,0.2);
}
.details-section h3 {
    color: var(--vybe-orange);
    margin-top: 0;
    margin-bottom: 15px;
}
.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.detail-item {
    color: var(--vybe-muted);
}
.detail-label {
    font-weight: 600;
    color: var(--vybe-text);
    display: block;
    margin-bottom: 5px;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
}
.items-table th {
    text-align: left;
    padding: 12px;
    background: rgba(199,185,255,0.05);
    color: var(--vybe-text);
    font-weight: 600;
    border-bottom: 2px solid rgba(199,185,255,0.2);
}
.items-table td {
    padding: 15px 12px;
    color: var(--vybe-muted);
    border-bottom: 1px solid rgba(199,185,255,0.1);
}
.items-table tr:last-child td {
    border-bottom: none;
}
.order-summary {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid rgba(199,185,255,0.2);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    color: var(--vybe-muted);
}
.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--vybe-orange);
    margin-top: 15px;
}
.order-status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: capitalize;
}
.status-pending {
    background: rgba(237,137,54,0.2);
    color: #ed8936;
    border: 1px solid rgba(237,137,54,0.4);
}
.status-processing {
    background: rgba(66,153,225,0.2);
    color: #4299e1;
    border: 1px solid rgba(66,153,225,0.4);
}
.status-shipped {
    background: rgba(159,122,234,0.2);
    color: #9f7aea;
    border: 1px solid rgba(159,122,234,0.4);
}
.status-delivered {
    background: rgba(72,187,120,0.2);
    color: #48bb78;
    border: 1px solid rgba(72,187,120,0.4);
}
.status-cancelled {
    background: rgba(245,101,101,0.2);
    color: #f56565;
    border: 1px solid rgba(245,101,101,0.4);
}
</style>

<div class="order-details-container">
    <a href="index.php?content=orderhistory" class="back-link">← Back to Order History</a>
    
    <div class="order-details-header">
        <h1>Order #<?php echo htmlspecialchars($order['orderNumber']); ?></h1>
        <div class="order-details-subtitle">
            <span><?php echo date('F j, Y g:i A', strtotime($order['orderDate'])); ?></span>
            <span class="order-status-badge status-<?php echo htmlspecialchars($order['orderStatus']); ?>">
                <?php echo htmlspecialchars($order['orderStatus']); ?>
            </span>
        </div>
    </div>
    
    <div class="details-grid">
        <div class="details-section">
            <h3>Shipping Information</h3>
            <div class="detail-item">
                <span class="detail-label">Name</span>
                <?php echo htmlspecialchars($order['fullName']); ?>
            </div>
            <div class="detail-item" style="margin-top: 15px;">
                <span class="detail-label">Address</span>
                <?php echo htmlspecialchars($order['shippingAddress']); ?>
            </div>
            <?php if (!empty($order['phoneNumber'])): ?>
                <div class="detail-item" style="margin-top: 15px;">
                    <span class="detail-label">Phone</span>
                    <?php echo htmlspecialchars($order['phoneNumber']); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="details-section">
            <h3>Payment Information</h3>
            <div class="detail-item">
                <span class="detail-label">Payment Method</span>
                <?php 
                if ($order['paymentMethod'] === 'paypal') {
                    echo '🅿️ PayPal';
                    if (!empty($order['paypalEmail'])) {
                        echo '<br><small>' . htmlspecialchars($order['paypalEmail']) . '</small>';
                    }
                } else {
                    echo '💳 Credit Card';
                }
                ?>
            </div>
            <div class="detail-item" style="margin-top: 15px;">
                <span class="detail-label">Email</span>
                <?php echo htmlspecialchars($order['emailAddress']); ?>
            </div>
        </div>
    </div>
    
    <div class="details-section">
        <h3>Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">$<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="order-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>FREE</span>
            </div>
            <div class="summary-row">
                <span>Tax</span>
                <span>$<?php echo number_format($order['tax'], 2); ?></span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span>$<?php echo number_format($order['total'], 2); ?></span>
            </div>
        </div>
    </div>
</div>
