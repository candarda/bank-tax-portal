<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vodafone_portal";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
$conn->query($sql);

// Select the database
$conn->select_db($dbname);

// Create banks table
$sql = "CREATE TABLE IF NOT EXISTS banks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$conn->query($sql);

// Add default banks if the table is empty
$check_banks = $conn->query("SELECT COUNT(*) as count FROM banks");
$bank_count = $check_banks->fetch_assoc()['count'];

if ($bank_count == 0) {
    $default_banks = [
        'Deutsche Bank',
        'Commerzbank',
        'Sparkasse',
        'Volksbank',
        'Postbank',
        'HypoVereinsbank',
        'DKB (Deutsche Kreditbank)',
        'ING-DiBa',
        'Targobank',
        'Santander Bank'
    ];
    
    $stmt = $conn->prepare("INSERT INTO banks (name) VALUES (?)");
    foreach ($default_banks as $bank) {
        $stmt->bind_param('s', $bank);
        $stmt->execute();
    }
}

// Create customer_data table
$sql = "CREATE TABLE IF NOT EXISTS customer_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    house_number VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    city VARCHAR(255) NOT NULL,
    bank_id INT NOT NULL,
    birth_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$conn->query($sql);

// Create admin table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$conn->query($sql);

// Create default admin user if not exists
$admin_check = $conn->query("SELECT id FROM admins WHERE username = 'admin' LIMIT 1");
if ($admin_check->num_rows === 0) {
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (username, password) VALUES ('admin', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_pass);
    $stmt->execute();
}

// Set charset
$conn->set_charset("utf8mb4");
?>
