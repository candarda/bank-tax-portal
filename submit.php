<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $birth_date = $_POST['birth_date'];
    $billing_address = trim($_POST['billing_address']);
    $created_at = date('Y-m-d H:i:s');

    try {
        // Verileri veritabanına kaydet
        $stmt = $conn->prepare("INSERT INTO customer_data (first_name, last_name, email, phone, password, address, birth_date, billing_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $phone, $password, $address, $birth_date, $billing_address, $created_at);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Vielen Dank! Ihre Informationen wurden erfolgreich gespeichert.'
            ]);
        } else {
            throw new Exception('Beim Speichern Ihrer Daten ist ein Fehler aufgetreten.');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
} 