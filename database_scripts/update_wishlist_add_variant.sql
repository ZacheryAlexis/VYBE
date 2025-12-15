-- Add variantID to wishlist so users can save specific variants (e.g., 10ml)
ALTER TABLE wishlist ADD COLUMN variantID INT(11) NULL;

-- Drop old unique key if exists and add a new unique constraint including variantID
ALTER TABLE wishlist DROP INDEX unique_wishlist;
ALTER TABLE wishlist ADD UNIQUE KEY unique_wishlist (userID, itemID, variantID);
