CREATE DATABASE IF NOT EXISTS password_manager;
USE password_manager;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    master_password_hash VARCHAR(255) NOT NULL,
    encryption_key VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    site_name VARCHAR(100) NOT NULL,
    site_url VARCHAR(255),
    username VARCHAR(100) NOT NULL,
    encrypted_password TEXT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (username, master_password_hash, encryption_key) 
VALUES ('testuser', '$2y$12$6E8L9XqyW5rJ1vZ7Y2bZ.e5VJ9QwXj3nTkK7d8YhG2fL3mN4vB5c6', 'base64encodedencryptionkeyhere');

INSERT INTO passwords (user_id, site_name, site_url, username, encrypted_password, notes)
VALUES 
(1, 'Google', 'https://google.com', 'testuser@gmail.com', 'encryptedpasswordhere', 'Personal account'),
(1, 'GitHub', 'https://github.com', 'testuser', 'encryptedpasswordhere', 'Work projects'),
(1, 'Twitter', 'https://twitter.com', '@testuser', 'encryptedpasswordhere', NULL),
(1, 'Amazon', 'https://amazon.com', 'testuser@example.com', 'encryptedpasswordhere', 'Prime account'),
(1, 'Netflix', 'https://netflix.com', 'testuser@example.com', 'encryptedpasswordhere', 'Family plan');