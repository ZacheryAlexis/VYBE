# Database Setup Instructions for New Features

This file contains instructions for setting up the database for the new Order History, Wishlist, and Inventory Management features.

## Run These SQL Scripts in Order:

### 1. Add Stock Quantity to Items Table
```sql
-- File: database_scripts/add_stock_quantity.sql
-- This adds a stockQuantity column to track inventory levels

ALTER TABLE items ADD COLUMN stockQuantity INT(11) DEFAULT 0;

-- Update existing items with stock quantities
UPDATE items SET stockQuantity = 25 WHERE itemID = 2000;
UPDATE items SET stockQuantity = 30 WHERE itemID = 2001;
UPDATE items SET stockQuantity = 0 WHERE itemID = 2002; -- Out of stock example
UPDATE items SET stockQuantity = 15 WHERE itemID = 2003;
UPDATE items SET stockQuantity = 20 WHERE itemID = 2004;

-- Set default stock for any other items
UPDATE items SET stockQuantity = 50 WHERE stockQuantity IS NULL OR stockQuantity = 0;
```

### 2. Create Orders Tables
```sql
-- File: database_scripts/create_orders.sql
-- This creates tables to store customer orders and order items

CREATE TABLE IF NOT EXISTS orders (
    orderID INT(11) AUTO_INCREMENT PRIMARY KEY,
    orderNumber VARCHAR(50) NOT NULL UNIQUE,
    userID INT(11) NULL,
    emailAddress VARCHAR(255) NOT NULL,
    fullName VARCHAR(255) NOT NULL,
    shippingAddress TEXT NOT NULL,
    phoneNumber VARCHAR(20),
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    paymentMethod ENUM('card', 'paypal') DEFAULT 'card',
    paypalEmail VARCHAR(255) NULL,
    orderDate DATETIME NOT NULL,
    orderStatus ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    orderItemID INT(11) AUTO_INCREMENT PRIMARY KEY,
    orderID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    itemName VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(orderID) ON DELETE CASCADE
);

CREATE INDEX idx_user_orders ON orders(userID);
CREATE INDEX idx_order_date ON orders(orderDate);
```

### 3. Create Wishlist Table
```sql
-- File: database_scripts/create_wishlist.sql
-- This creates a table to store user wishlists/favorites

CREATE TABLE IF NOT EXISTS wishlist (
    wishlistID INT(11) AUTO_INCREMENT PRIMARY KEY,
    userID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    addedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (userID, itemID)
);

CREATE INDEX idx_user_wishlist ON wishlist(userID);
```

## Quick Setup (Copy/Paste into MySQL):

If you want to run all scripts at once, execute this complete setup:

```sql
-- COMPLETE SETUP - RUN ALL AT ONCE

-- 1. Add stock quantity column
ALTER TABLE items ADD COLUMN IF NOT EXISTS stockQuantity INT(11) DEFAULT 0;

UPDATE items SET stockQuantity = 25 WHERE itemID = 2000;
UPDATE items SET stockQuantity = 30 WHERE itemID = 2001;
UPDATE items SET stockQuantity = 0 WHERE itemID = 2002;
UPDATE items SET stockQuantity = 15 WHERE itemID = 2003;
UPDATE items SET stockQuantity = 20 WHERE itemID = 2004;
UPDATE items SET stockQuantity = 50 WHERE (stockQuantity IS NULL OR stockQuantity = 0) AND itemID NOT IN (2000,2001,2002,2003,2004);

-- 2. Create orders tables
CREATE TABLE IF NOT EXISTS orders (
    orderID INT(11) AUTO_INCREMENT PRIMARY KEY,
    orderNumber VARCHAR(50) NOT NULL UNIQUE,
    userID INT(11) NULL,
    emailAddress VARCHAR(255) NOT NULL,
    fullName VARCHAR(255) NOT NULL,
    shippingAddress TEXT NOT NULL,
    phoneNumber VARCHAR(20),
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    paymentMethod ENUM('card', 'paypal') DEFAULT 'card',
    paypalEmail VARCHAR(255) NULL,
    orderDate DATETIME NOT NULL,
    orderStatus ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    orderItemID INT(11) AUTO_INCREMENT PRIMARY KEY,
    orderID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    itemName VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(orderID) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_user_orders ON orders(userID);
CREATE INDEX IF NOT EXISTS idx_order_date ON orders(orderDate);

-- 3. Create wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    wishlistID INT(11) AUTO_INCREMENT PRIMARY KEY,
    userID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    addedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (userID, itemID)
);

CREATE INDEX IF NOT EXISTS idx_user_wishlist ON wishlist(userID);
```

## Features Added:

### 1. **Order History**
- Users can view all their past orders at `index.php?content=orderhistory`
- Each order shows order number, date, status, total amount, and item count
- Click "View Details" to see full order information including items, shipping, and payment
- Orders are automatically saved when checkout is completed (both card and PayPal)
- Link added to header navigation for logged-in users

### 2. **Wishlist/Favorites**
- Users can save items to wishlist from product detail pages
- Access wishlist at `index.php?content=wishlist`
- Shows stock status for each wishlist item
- Can add items to cart directly from wishlist
- Remove items from wishlist with one click
- Heart icon (♥) in header navigation for logged-in users

### 3. **Inventory Management**
- Stock quantity tracked for all items
- Products show stock badges:
  - "In Stock" (green) - 10+ items available
  - "Only X left" (orange) - Less than 10 items
  - "Out of Stock" (red) - 0 items available
- Stock badges appear on:
  - Product detail pages
  - Search results
  - Wishlist items
- "Add to Cart" button disabled for out-of-stock items
- Example: Item 2002 (Freshman Sunrise) is set to 0 stock to demonstrate

## Testing the Features:

1. **Test Stock Display**: Visit search or product pages to see stock badges
2. **Test Out of Stock**: Try to add item 2002 to cart (should be disabled)
3. **Test Wishlist**: Log in and add items to wishlist from product pages
4. **Test Order History**: Complete a checkout and view in order history
5. **Admin Stock Management**: Use admin panel to update stock quantities

## Files Created/Modified:

### Database Scripts:
- `database_scripts/add_stock_quantity.sql`
- `database_scripts/create_orders.sql`
- `database_scripts/create_wishlist.sql`
- `database_scripts/items.sql` (updated with stockQuantity)

### Wishlist Files:
- `inventory/wishlist.inc.php` - Wishlist page
- `inventory/addwishlist.inc.php` - Add to wishlist handler
- `inventory/removewishlist.inc.php` - Remove from wishlist handler

### Order History Files:
- `inventory/orderhistory.inc.php` - Order history list page
- `inventory/orderdetails.inc.php` - Individual order details page
- `inventory/processorder.inc.php` - Updated to save orders to database
- `inventory/processpaypal.inc.php` - Updated to save PayPal orders

### Updated Files:
- `inventory/item.php` - Added stockQuantity property to Item class
- `inventory/displayitem.inc.php` - Shows stock status and wishlist button
- `inventory/search.inc.php` - Shows stock badges on search results
- `inventory/header.inc.php` - Added wishlist and orders links for logged-in users

All features are fully integrated and ready to use!
