<style>
.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.nav-menu li {
    margin-bottom: 8px;
}
.nav-menu a {
    display: block;
    padding: 12px 16px;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.nav-menu a:hover {
    background: rgba(199,185,255,0.1);
    padding-left: 20px;
    box-shadow: 0 0 20px rgba(199,185,255,0.3);
    border-left: 3px solid var(--vybe-border);
}
.nav-menu .section-header {
    color: var(--vybe-orange);
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 20px 0 10px 0;
    padding-left: 8px;
}
.nav-menu .indent {
    padding-left: 32px;
}
.cart-badge {
    background: var(--vybe-orange);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-left: 8px;
    box-shadow: 0 0 15px rgba(199,185,255,0.6);
    animation: pulse 2s ease-in-out infinite;
}
.search-form {
    margin-top: 20px;
    padding: 16px;
    background: rgba(0,0,0,0.2);
    border-radius: 8px;
}
.search-form label {
    display: block;
    color: var(--vybe-muted);
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-family: 'Avenir', 'Avenir Next', sans-serif;
}
.search-form input[type="text"],
.search-form input[type="number"] {
    width: calc(100% - 16px);
    margin-bottom: 8px;
}
</style>

<?php
if (!empty($_SESSION['user_name'])) {
    echo "<div style='padding: 16px; color: var(--vybe-orange); font-weight: 600; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 16px;'>";
    echo "Hello, " . htmlspecialchars($_SESSION['user_name']);
    echo "</div>\n";
}

if (empty($_SESSION['is_admin'])) {
    // Regular user navigation
?>
    <ul class="nav-menu">
        <li><a href="index.php">🏠 Home</a></li>
        <li><a href="index.php?content=listitems">🧴 Browse Scents</a></li>
        <li>
            <a href="index.php?content=cart">🛒 Shopping Cart
            <?php 
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $cartCount += $item['quantity'];
                    }
                }
                if ($cartCount > 0) echo "<span class='cart-badge'>$cartCount</span>";
            ?>
            </a>
        </li>
        <?php if (!empty($_SESSION['user_id'])) { ?>
        <li><a href="index.php?content=quiz">✨ Take Quiz</a></li>
        <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="index.php?content=logout" style="color: var(--vybe-orange);">🚪 Sign Out</a>
        </li>
        <?php } ?>
    </ul>
<?php
} else {
    // Admin navigation
?>
    <div style="padding: 16px; background: rgba(199,185,255,0.12); color: var(--vybe-orange); font-weight: 700; font-size: 1rem; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(199,185,255,0.3);">
        ⚙️ Admin Panel
    </div>
    
    <ul class="nav-menu">
        <li><a href="index.php">🏠 Home</a></li>
        
        <div class="section-header">Categories</div>
        <li><a href="index.php?content=listcategories" class="indent">📋 List Categories</a></li>
        <li><a href="index.php?content=newcategory" class="indent">➕ Add Category</a></li>
        
        <div class="section-header">Items</div>
        <li><a href="index.php?content=listitems" class="indent">📋 List Items</a></li>
        <li><a href="index.php?content=newitem" class="indent">➕ Add Item</a></li>
        
        <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="index.php?content=logout" style="color: var(--vybe-orange);">🚪 Sign Out</a>
        </li>
    </ul>
    
    <div class="search-form">
        <form action="index.php" method="post">
            <label>Quick Item Search:</label>
            <input type="number" name="itemID" placeholder="Item ID" required />
            <input type="submit" value="Find Item" style="width: 100%; margin-top: 4px;" />
            <input type="hidden" name="content" value="updateitem" />
        </form>
    </div>
    
    <div class="search-form">
        <form action="index.php" method="post">
            <label>Quick Category Search:</label>
            <input type="number" name="categoryID" placeholder="Category ID" required />
            <input type="submit" value="Find Category" style="width: 100%; margin-top: 4px;" />
            <input type="hidden" name="content" value="displaycategory" />
        </form>
    </div>
<?php
}
?>
