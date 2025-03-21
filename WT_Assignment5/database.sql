CREATE DATABASE manatee_db;
USE manatee_db;

CREATE TABLE manatees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    location VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE manatees
ADD COLUMN weight DECIMAL(5,2) DEFAULT NULL,
ADD COLUMN gender ENUM('Male', 'Female', 'Unknown') DEFAULT 'Unknown';
