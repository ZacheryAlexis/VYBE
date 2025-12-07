-- Create orders table to store order information
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

-- Create order_items table to store individual items in each order
CREATE TABLE IF NOT EXISTS order_items (
    orderItemID INT(11) AUTO_INCREMENT PRIMARY KEY,
    orderID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    itemName VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(orderID) ON DELETE CASCADE
);

-- Create index for faster queries
CREATE INDEX idx_user_orders ON orders(userID);
CREATE INDEX idx_order_date ON orders(orderDate);
