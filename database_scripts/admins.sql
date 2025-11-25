
SHOW DATABASES;

CREATE TABLE IF NOT EXISTS admins (
 adminID      INT(11)      NOT NULL   AUTO_INCREMENT,
 emailAddress VARCHAR(255) NOT NULL   UNIQUE,
 password     CHAR(64)     NOT NULL,
 firstName    VARCHAR(60)  NOT NULL,
 lastName     VARCHAR(60)  NOT NULL,
  PRIMARY KEY (adminID)
);

-- Sample admin accounts for Vybe (passwords are SHA2 hashed examples)
INSERT INTO admins
(emailAddress, password, firstName, lastName)
VALUES
('admin@vybe.co', SHA2('vybeAdmin123', 256), 'Alex', 'Mason');

SELECT emailAddress, firstName FROM admins ORDER BY firstName;

-- Example update (uncomment to run)
-- UPDATE admins SET emailAddress = 'ops@vybe.co' WHERE adminID = 1;
