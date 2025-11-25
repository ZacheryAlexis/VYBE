CREATE TABLE IF NOT EXISTS items (
 itemID           INT(11)        NOT NULL,
 itemName         VARCHAR(255)   NOT NULL,
 categoryID       INT(11)        NOT NULL,
 listPrice        DECIMAL(10,2)  NOT NULL,
 description      TEXT,
 PRIMARY KEY (itemID)
);

-- Sample perfume items for Vybe
INSERT INTO items
(itemID, itemName, categoryID, listPrice, description)
VALUES
(2000, 'Vybe Essence — Signature', 10, 39.99, 'A fresh woody-amber scent made for daytime classes and after-hours hangs.'),
(2001, 'Vybe Pocket Mist — Travel', 20, 9.99, 'Compact spray for quick refresh between lectures.'),
(2002, 'Vybe Night Swipe — Limited', 30, 49.99, 'Bold nocturnal fragrance with spicy and musk notes.'),
(2003, 'Vybe Campus Fresh — Budget', 40, 14.99, 'Affordable, uplifting citrus scent for everyday use.'),
(2004, 'Vybe Study Blend — Focus', 10, 24.99, 'Subtle green and citrus accord designed to uplift focus.');

SELECT * from items;

SELECT * FROM items JOIN categories ON items.categoryID = categories.categoryID;

-- Optional cleanup example
-- DELETE from items where itemID = 2004;
