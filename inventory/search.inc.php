<?php
require_once('database.php');

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<style>
.search-results {
    padding: 30px;
}
.search-results h2 {
    color: var(--vybe-text);
    font-family: 'Avenir', 'Avenir Next', sans-serif;
    font-size: 1.8rem;
    margin-bottom: 10px;
}
.search-query {
    color: var(--vybe-orange);
    font-weight: 600;
}
.search-meta {
    color: var(--vybe-muted);
    font-size: 0.95rem;
    margin-bottom: 30px;
}
.search-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 30px;
}
.search-item {
    background: var(--vybe-card);
    border: 1px solid rgba(199,185,255,0.2);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}
.search-item:hover {
    transform: translateY(-4px);
    border-color: var(--vybe-orange);
    box-shadow: 0 8px 24px rgba(199,185,255,0.25);
}
.search-item h3 {
    color: var(--vybe-text);
    font-size: 1.3rem;
    margin: 0 0 8px 0;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.search-item .category {
    display: inline-block;
    background: rgba(199,185,255,0.15);
    color: var(--vybe-orange);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 12px;
}
.search-item .price {
    color: var(--vybe-orange);
    font-size: 1.4rem;
    font-weight: 700;
    margin: 12px 0;
}
.search-item .description {
    color: var(--vybe-muted);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 16px;
}
.search-item .view-btn {
    display: inline-block;
    background: var(--vybe-orange);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    text-align: center;
}
.search-item .view-btn:hover {
    background: #b8a3ff;
    box-shadow: 0 4px 12px rgba(199,185,255,0.35);
    transform: translateY(-2px);
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
.no-results {
    text-align: center;
    padding: 60px 20px;
}
.no-results svg {
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}
.no-results h3 {
    color: var(--vybe-text);
    font-size: 1.5rem;
    margin-bottom: 12px;
}
.no-results p {
    color: var(--vybe-muted);
    font-size: 1rem;
    margin-bottom: 24px;
}
.no-results a {
    display: inline-block;
    background: var(--vybe-orange);
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}
.no-results a:hover {
    background: #b8a3ff;
    box-shadow: 0 4px 12px rgba(199,185,255,0.35);
}
</style>

<div class="search-results">
<?php
if (empty($searchQuery)) {
    echo '<div class="no-results">';
    echo '<h3>Enter a search term</h3>';
    echo '<p>Try searching for scent names, notes, or categories</p>';
    echo '<a href="index.php?content=listitems">Browse All Scents</a>';
    echo '</div>';
} else {
    // Search items by name or description
    $db = getDB();
    $searchTerm = '%' . $searchQuery . '%';
    
    $query = "
        SELECT i.*, c.categoryName 
        FROM items i 
        LEFT JOIN categories c ON i.categoryID = c.categoryID 
        WHERE i.itemName LIKE ? 
           OR i.description LIKE ?
        ORDER BY 
            CASE 
                WHEN i.itemName LIKE ? THEN 1
                ELSE 2
            END,
            i.itemName ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);
    $db->close();
    
    $resultCount = count($results);
    
    echo '<h2>Search Results for <span class="search-query">"' . htmlspecialchars($searchQuery) . '"</span></h2>';
    echo '<div class="search-meta">' . $resultCount . ' scent' . ($resultCount !== 1 ? 's' : '') . ' found</div>';
    
    if ($resultCount > 0) {
        echo '<div class="search-grid">';
        foreach ($results as $item) {
            $stockQty = $item['stockQuantity'] ?? 0;
            echo '<div class="search-item">';
            
            // Stock badge
            if ($stockQty <= 0) {
                echo '<span class="stock-badge out-of-stock">Out of Stock</span>';
            } elseif ($stockQty < 10) {
                echo '<span class="stock-badge low-stock">Only ' . $stockQty . ' left</span>';
            } else {
                echo '<span class="stock-badge in-stock">In Stock</span>';
            }
            
            echo '<div class="category">' . htmlspecialchars($item['categoryName'] ?? 'Uncategorized') . '</div>';
            echo '<h3>' . htmlspecialchars($item['itemName']) . '</h3>';
            
            if (!empty($item['description'])) {
                echo '<div class="description">' . htmlspecialchars($item['description']) . '</div>';
            }
            
            echo '<div class="price">$' . number_format($item['listPrice'], 2) . '</div>';
            echo '<a href="index.php?content=displayitem&itemID=' . $item['itemID'] . '" class="view-btn">View Details</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="no-results">';
        echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<circle cx="11" cy="11" r="8"></circle>';
        echo '<path d="m21 21-4.35-4.35"></path>';
        echo '</svg>';
        echo '<h3>No scents found</h3>';
        echo '<p>Try different keywords or browse all products</p>';
        echo '<a href="index.php?content=listitems">Browse All Scents</a>';
        echo '</div>';
    }
}
?>
</div>
