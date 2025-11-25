CREATE TABLE IF NOT EXISTS categories (
 categoryID       INT(11)        NOT NULL,
 categoryCode     VARCHAR(20)    NOT NULL,
 categoryName     VARCHAR(255)   NOT NULL,
 PRIMARY KEY (categoryID)
);

-- Perfume categories for Vybe
INSERT INTO categories (categoryID, categoryCode, categoryName) VALUES
(10, 'SIG', 'Signature'),
(20, 'TRV', 'Travel'),
(30, 'LTD', 'Limited Edition'),
(40, 'BUD', 'Budget');

SELECT * FROM categories;
