<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href=\"index.php?content=user_login\">log in</a> to view your order history.</p>";
    return;
}

require_once('database.php');

$db = getDB();
$stmt = $db->prepare("
    SELECT o.orderID, o.orderNumber, o.orderDate, o.total, o.orderStatus, o.paymentMethod,
           COUNT(oi.orderItemID) as itemCount
    FROM orders o
    LEFT JOIN order_items oi ON o.orderID = oi.orderID
    WHERE o.userID = ?
    GROUP BY o.orderID
    ORDER BY o.orderDate DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$db->close();
?>

<style>
.order-history-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px;
}
.order-history-header {
    margin-bottom: 30px;
}
.order-history-header h1 {
    color: var(--vybe-orange);
    font-size: 2rem;
    margin: 0 0 10px 0;
}
.order-history-header p {
    color: var(--vybe-muted);
}
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.order-card {
    background: var(--vybe-card);
    border: 1px solid rgba(199,185,255,0.2);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.order-card:hover {
    border-color: rgba(199,185,255,0.4);
    box-shadow: 0 4px 15px rgba(199,185,255,0.15);
}
.order-header {
    background: rgba(199,185,255,0.05);
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 15px;
    align-items: center;
    border-bottom: 1px solid rgba(199,185,255,0.1);
}
.order-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.order-number {
    color: var(--vybe-orange);
    font-size: 1.2rem;
    font-weight: 700;
}
.order-date {
    color: var(--vybe-muted);
    font-size: 0.95rem;
}
.order-status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: capitalize;
    white-space: nowrap;
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
.order-body {
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 20px;
    align-items: center;
}
.order-detail {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.order-detail-label {
    color: var(--vybe-muted);
    font-size: 0.9rem;
}
.order-detail-value {
    color: var(--vybe-text);
    font-weight: 600;
    font-size: 1.1rem;
}
.view-order-btn {
    background: linear-gradient(135deg, var(--vybe-orange) 0%, var(--vybe-accent) 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}
.view-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199,185,255,0.3);
}
.empty-orders {
    text-align: center;
    padding: 60px 20px;
}
.empty-orders-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}
.empty-orders h2 {
    color: var(--vybe-text);
    margin-bottom: 10px;
}
.empty-orders p {
    color: var(--vybe-muted);
    margin-bottom: 25px;
}
.shop-btn {
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
.shop-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199,185,255,0.3);
}
@media (max-width: 768px) {
    .order-header {
        grid-template-columns: 1fr;
    }
    .order-body {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="order-history-container">
    <div class="order-history-header">
        <h1>Order History</h1>
        <p>View and track all your past orders</p>
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <div class="empty-orders-icon">📦</div>
            <h2>No orders yet</h2>
            <p>Start exploring our collection and place your first order!</p>
            <a href="index.php" class="shop-btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <span class="order-number">Order #<?php echo htmlspecialchars($order['orderNumber']); ?></span>
                            <span class="order-date"><?php echo date('F j, Y g:i A', strtotime($order['orderDate'])); ?></span>
                        </div>
                        <span class="order-status-badge status-<?php echo htmlspecialchars($order['orderStatus']); ?>">
                            <?php echo htmlspecialchars($order['orderStatus']); ?>
                        </span>
                    </div>
                    
                    <div class="order-body">
                        <div class="order-detail">
                            <span class="order-detail-label">Total Amount</span>
                            <span class="order-detail-value">$<?php echo number_format($order['total'], 2); ?></span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Items</span>
                            <span class="order-detail-value"><?php echo $order['itemCount']; ?> item<?php echo $order['itemCount'] !== 1 ? 's' : ''; ?></span>
                        </div>
                        <a href="index.php?content=orderdetails&orderID=<?php echo $order['orderID']; ?>" class="view-order-btn">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
