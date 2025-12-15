<?php
require_once('security.php');
require_once('item.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? array();
$total = 0;
?>

<style>
.cart-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}
.cart-header {
    color: var(--vybe-orange);
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.cart-empty {
    padding: 40px;
    text-align: center;
    background: #141417;
    border-radius: 12px;
    border: 1px solid var(--vybe-border);
}
.cart-item {
    display: grid;
    grid-template-columns: 120px 2fr 1fr 1fr 1fr auto;
    gap: 15px;
    align-items: center;
    padding: 20px;
    margin-bottom: 15px;
    background: #141417;
    border-radius: 12px;
    border-left: 4px solid var(--vybe-orange);
    border: 1px solid var(--vybe-border);
    border-left: 4px solid var(--vybe-orange);
}
.cart-item-name {
    font-weight: 600;
    color: var(--vybe-text);
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.cart-item-image {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: 8px;
    background: #0f0f11;
    padding: 8px;
    display: block;
}
.cart-item-price {
    color: var(--vybe-muted);
}
.cart-qty-control {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cart-qty-control input {
    width: 60px;
    text-align: center;
    padding: 5px;
}
.cart-qty-btn {
    background: #4a5568;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}
.cart-qty-btn:hover {
    background: #5a6578;
}
.cart-remove-btn {
    background: transparent;
    color: var(--vybe-orange);
    border: 1px solid var(--vybe-orange);
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
}
.cart-remove-btn:hover {
    background: var(--vybe-orange);
    color: white;
}
.cart-summary {
    background: #141417;
    padding: 25px;
    border-radius: 12px;
    margin-top: 30px;
    border: 1px solid var(--vybe-border);
}
.cart-total {
    font-size: 1.5rem;
    color: var(--vybe-orange);
    font-weight: 700;
    margin-bottom: 20px;
}
.cart-checkout-btn {
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
    box-shadow: 
        0 4px 15px rgba(199,185,255,0.4),
        inset 0 1px 0 rgba(255,255,255,0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.cart-checkout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 8px 25px rgba(199,185,255,0.6),
        0 0 30px rgba(199,185,255,0.5),
        inset 0 1px 0 rgba(255,255,255,0.3);
    background: linear-gradient(135deg, var(--vybe-accent) 0%, var(--vybe-orange) 100%);
}
</style>

<div class="cart-container">
    <h2 class="cart-header">Shopping Cart</h2>
    
    <?php if (!empty($_SESSION['checkout_error'])): ?>
        <div style="background: rgba(245,101,101,0.2); color: #f56565; padding: 15px; border-radius: 8px; border: 1px solid rgba(245,101,101,0.4); margin-bottom: 20px;">
            ⚠️ <?php echo htmlspecialchars($_SESSION['checkout_error']); unset($_SESSION['checkout_error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($cart)): ?>
        <div class="cart-empty">
            <h3 style="color: var(--vybe-muted);">Your cart is empty</h3>
            <p><a class="accent-link" href="index.php?content=listitems">Start Shopping →</a></p>
        </div>
    <?php else: ?>
        
        <?php foreach ($cart as $itemID => $item): ?>
            <?php 
            $itemTotal = $item['listPrice'] * $item['quantity'];
            $total += $itemTotal;
            ?>
            <div class="cart-item">
                <?php
                // Determine image for this cart row. Prefer variant imageSuffix when present.
                $imgPath = 'images/items.png';
                $origID = intval($item['itemID'] ?? $itemID);
                $variantID = !empty($item['variantID']) ? intval($item['variantID']) : null;
                $baseName = '';
                $variant = null;
                $origItemObj = null;
                if ($origID) {
                    $origItemObj = Item::findItem($origID);
                }
                if ($variantID) {
                    try { $variant = Item::getVariantByID($variantID); } catch (Exception $e) { $variant = null; }
                }
                if ($origItemObj) {
                    $baseName = str_replace(' ', '_', $origItemObj->itemName);
                    $basePngFs = __DIR__ . '/images/' . $baseName . '.png';
                    $baseSvgFs = __DIR__ . '/images/' . $baseName . '.svg';
                    // Try variant-specific
                    if (!empty($variant['imageSuffix'])) {
                        $sfx = $variant['imageSuffix'];
                        $pngFs = __DIR__ . '/images/' . $baseName . $sfx . '.png';
                        $svgFs = __DIR__ . '/images/' . $baseName . $sfx . '.svg';
                        if (file_exists($pngFs)) {
                            $imgPath = 'images/' . $baseName . $sfx . '.png';
                        } elseif (file_exists($svgFs)) {
                            $imgPath = 'images/' . $baseName . $sfx . '.svg';
                        }
                    }
                    // fallback to base
                    if ($imgPath === 'images/items.png') {
                        if (file_exists($basePngFs)) {
                            $imgPath = 'images/' . $baseName . '.png';
                        } elseif (file_exists($baseSvgFs)) {
                            $imgPath = 'images/' . $baseName . '.svg';
                        }
                    }
                }
                ?>
                <div><img class="cart-item-image" src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($item['itemName']); ?>"></div>
                <div class="cart-item-name"><?php echo htmlspecialchars($item['itemName']); ?></div>
                <div class="cart-item-price">$<?php echo number_format($item['listPrice'], 2); ?></div>
                <div class="cart-qty-control">
                    <form method="post" action="index.php?content=updatecart" style="display:inline;">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="itemID" value="<?php echo $itemID; ?>">
                        <input type="hidden" name="action" value="decrease">
                        <button type="submit" class="cart-qty-btn">−</button>
                    </form>
                    <input type="text" value="<?php echo $item['quantity']; ?>" readonly>
                    <form method="post" action="index.php?content=updatecart" style="display:inline;">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="itemID" value="<?php echo $itemID; ?>">
                        <input type="hidden" name="action" value="increase">
                        <button type="submit" class="cart-qty-btn">+</button>
                    </form>
                </div>
                <div class="cart-item-price" style="font-weight:600;">$<?php echo number_format($itemTotal, 2); ?></div>
                <form method="post" action="index.php?content=updatecart" style="display:inline;">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="itemID" value="<?php echo $itemID; ?>">
                    <input type="hidden" name="action" value="remove">
                    <button type="submit" class="cart-remove-btn">Remove</button>
                </form>
            </div>
        <?php endforeach; ?>
        
        <div class="cart-summary">
            <div class="cart-total">Total: $<?php echo number_format($total, 2); ?></div>
            <p style="color: var(--vybe-muted); margin-bottom: 20px;">
                <a href="index.php?content=listitems" style="color: var(--vybe-text);">← Continue Shopping</a>
            </p>
            <form method="post" action="index.php?content=checkout">
                <button type="submit" class="cart-checkout-btn">Proceed to Checkout</button>
            </form>
        </div>
        
    <?php endif; ?>
</div>
