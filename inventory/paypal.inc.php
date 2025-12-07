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
?>

<style>
.paypal-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 20px;
}
.paypal-header {
    text-align: center;
    margin-bottom: 30px;
}
.paypal-logo {
    font-size: 3rem;
    margin-bottom: 10px;
}
.paypal-title {
    color: var(--vybe-text);
    font-size: 1.5rem;
    margin-bottom: 5px;
}
.paypal-subtitle {
    color: var(--vybe-muted);
}
.paypal-card {
    background: #141417;
    border: 1px solid rgba(199,185,255,0.3);
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 20px;
}
.paypal-card h3 {
    color: var(--vybe-orange);
    margin-top: 0;
    margin-bottom: 20px;
}
.order-details {
    border-bottom: 1px solid rgba(199,185,255,0.2);
    padding-bottom: 15px;
    margin-bottom: 15px;
}
.order-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    color: var(--vybe-muted);
}
.order-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--vybe-text);
    margin-top: 15px;
}
.paypal-form {
    margin-top: 20px;
}
.paypal-input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    background: rgba(26,26,30,0.6);
    color: var(--vybe-text);
    border: 1px solid rgba(199,185,255,0.2);
    border-radius: 6px;
    font-size: 1rem;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.paypal-input:focus {
    outline: none;
    border-color: #0070ba;
    box-shadow: 0 0 0 3px rgba(0,112,186,0.2);
}
.paypal-btn {
    width: 100%;
    padding: 15px;
    background: #0070ba;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.paypal-btn:hover {
    background: #005a94;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,112,186,0.3);
}
.security-note {
    text-align: center;
    color: var(--vybe-muted);
    font-size: 0.9rem;
    margin-top: 20px;
}
.cancel-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: var(--vybe-text);
    text-decoration: none;
}
.cancel-link:hover {
    color: var(--vybe-orange);
}
</style>

<div class="paypal-container">
    <div class="paypal-header">
        <div class="paypal-logo">🅿️</div>
        <h2 class="paypal-title">PayPal Checkout</h2>
        <p class="paypal-subtitle">Complete your secure payment</p>
    </div>
    
    <div class="paypal-card">
        <h3>Order Summary</h3>
        <div class="order-details">
            <?php foreach ($cart as $item): ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['itemName']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['listPrice'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-item">
            <span>Subtotal</span>
            <span>$<?php echo number_format($total, 2); ?></span>
        </div>
        <div class="order-item">
            <span>Tax (7%)</span>
            <span>$<?php echo number_format($tax, 2); ?></span>
        </div>
        <div class="order-item">
            <span>Shipping</span>
            <span>FREE</span>
        </div>
        
        <div class="order-total">
            <span>Total</span>
            <span>$<?php echo number_format($grandTotal, 2); ?></span>
        </div>
    </div>
    
    <div class="paypal-card">
        <h3>PayPal Login</h3>
        <form class="paypal-form" action="index.php?content=processpaypal" method="POST">
            <input type="email" name="paypal_email" class="paypal-input" placeholder="Email address" required>
            <input type="password" name="paypal_password" class="paypal-input" placeholder="Password" required>
            
            <p style="color: var(--vybe-muted); font-size: 0.9rem; margin-bottom: 15px;">
                <em>Note: This is a simulated PayPal payment for academic purposes. Use any email/password to proceed.</em>
            </p>
            
            <button type="submit" class="paypal-btn">Pay $<?php echo number_format($grandTotal, 2); ?></button>
        </form>
        
        <a href="index.php?content=checkout" class="cancel-link">← Cancel and return to checkout</a>
    </div>
    
    <p class="security-note">
        🔒 Your payment information is encrypted and secure
    </p>
</div>
