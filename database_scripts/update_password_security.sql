-- Security Migration: Update Password Storage
-- Changes password field from CHAR(64) to VARCHAR(255) to support bcrypt hashes
-- IMPORTANT: Existing passwords will need to be reset after this migration

-- Backup current users table first!
-- CREATE TABLE users_backup AS SELECT * FROM users;

-- Update users table password field
ALTER TABLE users 
MODIFY COLUMN password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password';

-- Update admins table password field
ALTER TABLE admins 
MODIFY COLUMN password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password';

-- NOTE: All existing passwords are now invalid because they're SHA256 hashes
-- Users will need to reset passwords or you'll need to migrate them
-- For development, you can create a test user with bcrypt:

-- Test user with password 'student123'
-- Hash generated with: password_hash('student123', PASSWORD_BCRYPT)
UPDATE users 
SET password = '$2y$12$vURRuzQcb2Yz7QF3FcG/KOs6ldCNvxCp30zG5i407XfL1Fn0Vlzoq'
WHERE emailAddress = 'student1@vybe.test';

-- Admin with password 'vybeAdmin123'
-- Hash generated with: password_hash('vybeAdmin123', PASSWORD_BCRYPT)
UPDATE admins
SET password = '$2y$12$esj7GPgt1ngFCs6pw2Z6ou2kg/G2KXngZKKmSsIIUFjDBnMeM2kke'
WHERE emailAddress = 'admin@vybe.co';

-- Or insert new test user:
-- INSERT INTO users (emailAddress, password, firstName, lastName)
-- VALUES ('test@vybe.test', '$2y$12$LKzVx6QGJmUJ0qVGqxKYB.XxDMJJHK5x9HpYqL8QW5KzF1V9pHGGu', 'Test', 'User');
