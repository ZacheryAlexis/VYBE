-- Add cart column to users table to persist shopping cart across sessions
-- Run this migration to add cart storage to existing users table

ALTER TABLE users 
ADD COLUMN cart JSON DEFAULT NULL COMMENT 'Stores user shopping cart as JSON array';

-- Update existing users to have empty cart
UPDATE users SET cart = JSON_ARRAY() WHERE cart IS NULL;
