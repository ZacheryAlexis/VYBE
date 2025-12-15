-- Create item_variants table to store different sizes (e.g., 10ml minis)
CREATE TABLE IF NOT EXISTS item_variants (
    variantID INT(11) AUTO_INCREMENT PRIMARY KEY,
    itemID INT(11) NOT NULL,
    sizeLabel VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stockQuantity INT(11) DEFAULT 0,
    imageSuffix VARCHAR(64) DEFAULT NULL,
    FOREIGN KEY (itemID) REFERENCES items(itemID) ON DELETE CASCADE
);

-- Insert mini (10ml) variants for existing sample items (adjust prices as needed)
INSERT INTO item_variants (itemID, sizeLabel, price, stockQuantity, imageSuffix)
VALUES
(2000, '10ml', 14.99, 15, '_mini'),
(2001, '10ml', 14.99, 20, '_mini'),
(2002, '10ml', 14.99, 5, '_mini'),
(2003, '10ml', 14.99, 10, '_mini'),
(2004, '10ml', 14.99, 12, '_mini');
