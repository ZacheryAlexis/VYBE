<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> to view your wishlist.</p>";
    return;
}

require_once('database.php');

$db = getDB();
$stmt = $db->prepare("
    SELECT w.wishlistID, w.itemID, w.addedDate, i.itemName, i.listPrice, i.description, i.stockQuantity, c.categoryName
    FROM wishlist w
    JOIN items i ON w.itemID = i.itemID
    LEFT JOIN categories c ON i.categoryID = c.categoryID
    WHERE w.userID = ?
    ORDER BY w.addedDate DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$wishlistItems = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$db->close();
?>

<style>
.wishlist-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
}
.wishlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.wishlist-header h1 {
    color: var(--vybe-orange);
    font-size: 2rem;
    margin: 0;
}
.wishlist-count {
    color: var(--vybe-muted);
    font-size: 1.1rem;
}
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    margin-top: 20px;
}
.wishlist-item {
    background: var(--vybe-card);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    border: 1px solid rgba(199,185,255,0.2);
}
.wishlist-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(199,185,255,0.2);
    border-color: rgba(199,185,255,0.4);
}
.wishlist-item-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.wishlist-item-name {
    color: var(--vybe-text);
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 8px 0;
}
.wishlist-item-category {
    color: var(--vybe-muted);
    font-size: 0.9rem;
    background: rgba(199,185,255,0.1);
    padding: 4px 12px;
    border-radius: 12px;
    display: inline-block;
}
.wishlist-item-body {
    padding: 20px;
}
.wishlist-item-description {
    color: var(--vybe-muted);
    font-size: 0.95rem;
    margin-bottom: 15px;
    line-height: 1.5;
}
.wishlist-item-price {
    color: var(--vybe-orange);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}
.wishlist-item-actions {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
}
.add-to-cart-btn {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199,185,255,0.3);
}
.add-to-cart-btn:disabled {
    background: rgba(100,100,100,0.5);
    cursor: not-allowed;
    transform: none;
}
.remove-wishlist-btn {
    background: transparent;
    color: var(--vybe-muted);
    padding: 12px;
    border: 1px solid rgba(199,185,255,0.3);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.remove-wishlist-btn:hover {
    background: rgba(255,100,100,0.2);
    border-color: rgba(255,100,100,0.5);
    color: #ff6b6b;
}
.stock-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
}
.in-stock {
    background: rgba(72,187,120,0.2);
    color: #48bb78;
    border: 1px solid rgba(72,187,120,0.4);
}
.low-stock {
    background: rgba(237,137,54,0.2);
    color: #ed8936;
    border: 1px solid rgba(237,137,54,0.4);
}
.out-of-stock {
    background: rgba(245,101,101,0.2);
    color: #f56565;
    border: 1px solid rgba(245,101,101,0.4);
}
.empty-wishlist {
    text-align: center;
    padding: 60px 20px;
}
.empty-wishlist-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}
.empty-wishlist h2 {
    color: var(--vybe-text);
    margin-bottom: 10px;
}
.empty-wishlist p {
    color: var(--vybe-muted);
    margin-bottom: 25px;
}
.browse-btn {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}
.browse-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199,185,255,0.3);
}
.added-date {
    color: var(--vybe-muted);
    font-size: 0.85rem;
    margin-top: 10px;
}
</style>

<div class="wishlist-container">
    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <span class="wishlist-count"><?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?></span>
    </div>
    
    <?php if (!empty($_SESSION['wishlist_message']) || !empty($_SESSION['cart_message'])): ?>
        <div style="background: rgba(72,187,120,0.2); color: #48bb78; padding: 15px; border-radius: 8px; border: 1px solid rgba(72,187,120,0.4); margin-bottom: 20px;">
            <?php 
            echo htmlspecialchars($_SESSION['wishlist_message'] ?? $_SESSION['cart_message']); 
            unset($_SESSION['wishlist_message']);
            unset($_SESSION['cart_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($wishlistItems)): ?>
        <div class="empty-wishlist">
            <div class="empty-wishlist-icon">💜</div>
            <h2>Your wishlist is empty</h2>
            <p>Save your favorite scents here for easy access later!</p>
            <a href="index.php" class="browse-btn">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="wishlist-grid">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="wishlist-item">
                    <?php
                    $stockQty = $item['stockQuantity'];
                    if ($stockQty <= 0) {
                        echo '<span class="stock-badge out-of-stock">Out of Stock</span>';
                    } elseif ($stockQty < 10) {
                        echo '<span class="stock-badge low-stock">Only ' . $stockQty . ' left</span>';
                    } else {
                        echo '<span class="stock-badge in-stock">In Stock</span>';
                    }
                    ?>
                    
                    <div class="wishlist-item-header">
                        <h3 class="wishlist-item-name"><?php echo htmlspecialchars($item['itemName']); ?></h3>
                        <?php if (!empty($item['categoryName'])): ?>
                            <span class="wishlist-item-category"><?php echo htmlspecialchars($item['categoryName']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="wishlist-item-body">
                        <p class="wishlist-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="wishlist-item-price">$<?php echo number_format($item['listPrice'], 2); ?></div>
                        
                        <div class="wishlist-item-actions">
                            <?php if ($stockQty > 0): ?>
                                <form method="post" action="index.php?content=addtocart" style="margin: 0;">
                                    <?php require_once('security.php'); csrf_field(); ?>
                                    <input type="hidden" name="itemID" value="<?php echo $item['itemID']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="from_wishlist" value="1">
                                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <button class="add-to-cart-btn" disabled>Out of Stock</button>
                            <?php endif; ?>
                            
                            <form method="post" action="index.php?content=removewishlist" style="margin: 0;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="wishlistID" value="<?php echo $item['wishlistID']; ?>">
                                <button type="submit" class="remove-wishlist-btn" title="Remove from wishlist">🗑️</button>
                            </form>
                        </div>
                        
                        <p class="added-date">Added <?php echo date('M j, Y', strtotime($item['addedDate'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
