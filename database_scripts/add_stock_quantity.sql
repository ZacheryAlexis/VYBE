-- Add stockQuantity column to items table
ALTER TABLE items ADD COLUMN stockQuantity INT(11) DEFAULT 0;

-- Update existing items with stock quantities
UPDATE items SET stockQuantity = 25 WHERE itemID = 2000;
UPDATE items SET stockQuantity = 30 WHERE itemID = 2001;
UPDATE items SET stockQuantity = 0 WHERE itemID = 2002; -- Out of stock example
UPDATE items SET stockQuantity = 15 WHERE itemID = 2003;
UPDATE items SET stockQuantity = 20 WHERE itemID = 2004;

-- You can update other items as needed
UPDATE items SET stockQuantity = 50 WHERE stockQuantity IS NULL OR stockQuantity = 0;
