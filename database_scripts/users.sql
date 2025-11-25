-- users.sql
-- Creates a users table for Vybe and a read-only view + search stored procedure
-- NOTE: This script is intended as a schema + helper functions for local/dev MySQL.
-- Adjust types and permissions for your production environment.

CREATE TABLE IF NOT EXISTS users (
  userID INT(11) NOT NULL AUTO_INCREMENT,
  emailAddress VARCHAR(255) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL, -- SHA2-256 hashed password
  firstName VARCHAR(60) DEFAULT NULL,
  lastName VARCHAR(60) DEFAULT NULL,
  -- `preferences` will store quiz answers or inferred preferences as JSON.
  preferences JSON DEFAULT NULL,
  -- `quizResults` can keep the raw quiz answers and suggested scent id(s)
  quizResults JSON DEFAULT NULL,
  preferredCategories JSON DEFAULT NULL, -- array of categoryIDs the user likes
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (userID)
);

-- Example user (password is example unhashed value 'student123' hashed using SHA2)
INSERT INTO users (emailAddress, password, firstName, lastName, preferences, quizResults, preferredCategories)
VALUES
('student1@vybe.test', SHA2('student123', 256), 'Sam', 'Lee', JSON_OBJECT('scent_family','fresh','strength','light'), JSON_ARRAY(JSON_OBJECT('q1','citrus','q2','outgoing','suggested',2000)), JSON_ARRAY(10));

-- Create a read-only view of available items so users can search without modifying items/categories
DROP VIEW IF EXISTS available_items;
CREATE VIEW available_items AS
SELECT i.itemID, i.itemName, i.description, i.listPrice, c.categoryID, c.categoryName, c.categoryCode
FROM items i
JOIN categories c ON i.categoryID = c.categoryID;

-- Stored procedure for searching items by term (matches name, description, categoryName)
-- Usage: CALL search_items('citrus');
DELIMITER //
DROP PROCEDURE IF EXISTS search_items;//
CREATE PROCEDURE search_items(IN term VARCHAR(255))
BEGIN
  SET @s = CONCAT('%', term, '%');
  SELECT * FROM available_items
  WHERE itemName LIKE @s
     OR description LIKE @s
     OR categoryName LIKE @s;
END;//
DELIMITER ;

-- Notes:
-- - The `users` table stores `preferences` and `quizResults` as JSON so the application
--   can iterate questions/answers and store suggested scents (itemIDs) or categories.
-- - The `available_items` view provides a safer, read-only way to return item data to users.
-- - The `search_items` stored procedure is a convenience; alternatively, the application
--   can query the `available_items` view directly using parameterized queries.
-- - To create a read-only DB user for the application, run (example):
--   CREATE USER 'vybe_ro'@'localhost' IDENTIFIED BY 'choose_a_secure_password';
--   GRANT SELECT ON `your_db_name`.* TO 'vybe_ro'@'localhost';
--   (Customize privileges to only the `available_items` view if desired.)
