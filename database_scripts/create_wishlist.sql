-- Create wishlist table to store user favorites
CREATE TABLE IF NOT EXISTS wishlist (
    wishlistID INT(11) AUTO_INCREMENT PRIMARY KEY,
    userID INT(11) NOT NULL,
    itemID INT(11) NOT NULL,
    addedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (userID, itemID)
);

-- Create index for faster queries
CREATE INDEX idx_user_wishlist ON wishlist(userID);
