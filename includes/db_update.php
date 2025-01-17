<?php
require_once 'db.php';

// Forms tablosunu güncelle
$sql = "CREATE TABLE IF NOT EXISTS forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    iban VARCHAR(34) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bank_id) REFERENCES banks(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Forms table updated successfully";
} else {
    echo "Error updating forms table: " . $conn->error;
}
