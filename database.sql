-- Create the database
CREATE DATABASE IF NOT EXISTS bank_tax_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bank_tax_portal;

-- Create banks table
CREATE TABLE IF NOT EXISTS banks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create forms table
CREATE TABLE IF NOT EXISTS forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city_zip VARCHAR(100) NOT NULL,
    iban VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bank_id) REFERENCES banks(id)
) ENGINE=InnoDB;

-- Insert sample banks
INSERT INTO banks (name, logo) VALUES
('Ziraat Bankası', 'ziraat.png'),
('İş Bankası', 'is_bank.png');
