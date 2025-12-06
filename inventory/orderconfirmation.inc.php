<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$order = $_SESSION['last_order'] ?? null;

if (!$order) {
    header('Location: index.php');
    exit();
}
?>

<style>
.confirmation-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}
.confirmation-icon {
    font-size: 5rem;
    margin-bottom: 20px;
}
.confirmation-header {
    color: var(--vybe-orange);
    font-size: 2rem;
    margin-bottom: 10px;
}
.order-number {
    font-size: 1.3rem;
    color: var(--vybe-text);
    font-weight: 600;
    margin-bottom: 30px;
}
.confirmation-section {
    background: var(--vybe-card);
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: left;
}
.confirmation-section h3 {
    color: var(--vybe-orange);
    margin-top: 0;
    margin-bottom: 15px;
}
.order-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    color: var(--vybe-muted);
}
.order-item:last-child {
    border-bottom: none;
}
.order-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--vybe-orange);
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid var(--vybe-orange);
}
.info-row {
    margin-bottom: 10px;
    color: var(--vybe-muted);
}
.info-label {
    color: var(--vybe-text);
    font-weight: 600;
}
.continue-btn {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 15px 40px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-top: 30px;
    box-shadow: 0 4px 15px rgba(199,185,255,0.25);
    transition: all 0.3s ease;
}
.continue-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(199,185,255,0.35);
}
</style>

<div class="confirmation-container">
    <div class="confirmation-icon">✓</div>
    <h2 class="confirmation-header">Order Confirmed!</h2>
    <p class="order-number">Order #<?php echo $order['orderNumber']; ?></p>
    
    <div class="confirmation-section">
        <h3>Thank You for Your Order</h3>
        <p style="color: var(--vybe-muted);">
            Your order has been successfully placed. A confirmation email will be sent to 
            <strong style="color: var(--vybe-text);"><?php echo htmlspecialchars($order['shippingEmail']); ?></strong>
        </p>
    </div>
    
    <div class="confirmation-section">
        <h3>Order Details</h3>
        <div class="info-row">
            <span class="info-label">Order Date:</span> <?php echo $order['orderDate']; ?>
        </div>
        <div class="info-row">
            <span class="info-label">Estimated Delivery:</span> 3-5 Business Days
        </div>
    </div>
    
    <div class="confirmation-section">
        <h3>Items Ordered</h3>
        <?php foreach ($order['items'] as $item): ?>
            <div class="order-item">
                <span><?php echo htmlspecialchars($item['itemName']); ?> (x<?php echo $item['quantity']; ?>)</span>
                <span>$<?php echo number_format($item['listPrice'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endforeach; ?>
        
        <div class="order-item" style="margin-top: 15px;">
            <span>Subtotal</span>
            <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
        </div>
        <div class="order-item">
            <span>Tax</span>
            <span>$<?php echo number_format($order['tax'], 2); ?></span>
        </div>
        <div class="order-item">
            <span>Shipping</span>
            <span>FREE</span>
        </div>
        
        <div class="order-total">
            <span>Total</span>
            <span>$<?php echo number_format($order['total'], 2); ?></span>
        </div>
    </div>
    
    <div class="confirmation-section">
        <h3>Shipping Address</h3>
        <div class="info-row">
            <span class="info-label"><?php echo htmlspecialchars($order['shippingName']); ?></span>
        </div>
        <div class="info-row">
            <?php echo htmlspecialchars($order['shippingAddress']); ?>
        </div>
    </div>
    
    <a href="index.php" class="continue-btn">Continue Shopping</a>
</div>
