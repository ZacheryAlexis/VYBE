CREATE TABLE IF NOT EXISTS items (
 itemID           INT(11)        NOT NULL,
 itemName         VARCHAR(255)   NOT NULL,
 categoryID       INT(11)        NOT NULL,
 listPrice        DECIMAL(10,2)  NOT NULL,
 description      TEXT,
 stockQuantity    INT(11)        DEFAULT 0,
 PRIMARY KEY (itemID)
);

-- Clear old items first
DELETE FROM items WHERE itemID IN (2000, 2001, 2002, 2003, 2004);

-- Sample perfume items for Vybe - Matched to Quiz Results
INSERT INTO items
(itemID, itemName, categoryID, listPrice, description, stockQuantity)
VALUES
(2000, 'VYBE Signature', 10, 39.99, 'Lavender + Pear. Calm and balanced for the collected soul. Perfect for those peaceful mornings and quiet study corners.', 25),
(2001, 'Study Session', 10, 34.99, 'Peppermint + Eucalyptus. Sharp focus and mental clarity. Built for the library grind and those long study hours.', 30),
(2002, 'Freshman Sunrise', 10, 29.99, 'Citrus + Green Tea. Bright, energetic, ready to go. Fresh like a beach day or sunny outdoor spot.', 0),
(2003, 'All-Nighter', 10, 44.99, 'Cedar + Amber. Warm, cozy, confident. The mature friend at the café with long-lasting presence.', 15),
(2004, 'Dorm Crush', 10, 32.99, 'Peach + Hibiscus. Fun, sweet, youthful. For the extroverted social butterfly at every campus event.', 20);

SELECT * from items;

SELECT * FROM items JOIN categories ON items.categoryID = categories.categoryID;

-- Optional cleanup example
-- DELETE from items where itemID = 2004;
