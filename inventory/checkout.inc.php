<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? array();
$total = 0;

if (empty($cart)) {
    header('Location: index.php?content=cart');
    exit();
}

foreach ($cart as $item) {
    $total += $item['listPrice'] * $item['quantity'];
}
?>

<style>
.checkout-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
.checkout-header {
    color: var(--vybe-orange);
    font-size: 1.8rem;
    margin-bottom: 30px;
}
.checkout-section {
    background: #141417;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid rgba(199,185,255,0.3);
}
.checkout-section h3 {
    color: var(--vybe-text);
    margin-top: 0;
    margin-bottom: 20px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    color: var(--vybe-text);
    margin-bottom: 8px;
    font-weight: 500;
}
.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    background: rgba(26,26,30,0.6);
    color: var(--vybe-text);
    border: 1px solid rgba(199,185,255,0.2);
    border-radius: 6px;
    font-size: 1rem;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--vybe-orange);
    box-shadow: 0 0 0 3px rgba(199,185,255,0.2);
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.order-summary {
    background: #141417;
    padding: 25px;
    border-radius: 8px;
    border: 1px solid rgba(199,185,255,0.3);
}
.order-summary h3 {
    color: var(--vybe-orange);
    margin-top: 0;
}
.summary-item {
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
    color: var(--vybe-text);
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid rgba(199,185,255,0.3);
}
.place-order-btn {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 15px 40px;
    border: none;
    border-radius: 8px;
    font-size: 1.2rem;
    font-weight: 700;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
    cursor: pointer;
    width: 100%;
    margin-top: 25px;
    box-shadow: 0 4px 15px rgba(199,185,255,0.25);
    transition: all 0.3s ease;
}
.place-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(199,185,255,0.35);
}
</style>

<?php require_once('security.php'); ?>
<div class="checkout-container">
    <h2 class="checkout-header">Checkout</h2>
    
    <form method="post" action="index.php?content=processorder">
        <?php csrf_field(); ?>
        
        <!-- Shipping Information -->
        <div class="checkout-section">
            <h3>Shipping Information</h3>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="fullName" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Street Address *</label>
                <input type="text" name="address" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>State *</label>
                    <input type="text" name="state" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>ZIP Code *</label>
                    <input type="text" name="zip" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone">
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="checkout-section">
            <h3>Payment Information</h3>
            <div class="form-group">
                <label>Cardholder Name *</label>
                <input type="text" name="cardName" required>
            </div>
            <div class="form-group">
                <label>Card Number *</label>
                <input type="text" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Expiration Date *</label>
                    <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="form-group">
                    <label>CVV *</label>
                    <input type="text" name="cvv" placeholder="123" maxlength="4" required>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cart as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['itemName']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['listPrice'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            
            <div class="summary-item" style="margin-top: 15px;">
                <span>Subtotal</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Shipping</span>
                <span>FREE</span>
            </div>
            <div class="summary-item">
                <span>Tax</span>
                <span>$<?php echo number_format($total * 0.07, 2); ?></span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>$<?php echo number_format($total * 1.07, 2); ?></span>
            </div>
            
            <button type="submit" class="place-order-btn">Place Order</button>
        </div>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="index.php?content=cart" style="color: var(--vybe-text);">← Back to Cart</a>
        </p>
        
    </form>
</div>
