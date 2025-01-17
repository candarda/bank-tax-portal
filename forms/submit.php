<?php
require_once '../includes/db.php';

// JSON header'ı ekle
header('Content-Type: application/json');

// Hata raporlamasını aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Form verilerini al
        $bank_id = isset($_POST['bank_id']) ? (int)$_POST['bank_id'] : 0;
        $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $contact_info = isset($_POST['contact_info']) ? trim($_POST['contact_info']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $zip_code = isset($_POST['zip_code']) ? trim($_POST['zip_code']) : '';
        $iban = isset($_POST['iban']) ? trim($_POST['iban']) : '';

        // Veri kontrolü
        if (!$bank_id || !$full_name || !$contact_info || !$address || !$city || !$zip_code || !$iban) {
            throw new Exception('Alle Felder müssen ausgefüllt werden');
        }

        // Banka ID'sinin geçerliliğini kontrol et
        $bank_check = $conn->prepare("SELECT id FROM banks WHERE id = ?");
        $bank_check->bind_param("i", $bank_id);
        $bank_check->execute();
        $bank_result = $bank_check->get_result();
        
        if ($bank_result->num_rows === 0) {
            throw new Exception('Ungültige Bank ID');
        }

        // City ve ZIP'i birleştir
        $city_zip = $zip_code . ' ' . $city;

        // Formu kaydet
        $stmt = $conn->prepare("INSERT INTO forms (bank_id, full_name, contact_info, address, city_zip, iban) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $bank_id, $full_name, $contact_info, $address, $city_zip, $iban);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Datenbankfehler: ' . $stmt->error);
        }
        
        $stmt->close();
    } else {
        throw new Exception('Ungültige Anfragemethode');
    }
} catch (Exception $e) {
    error_log('Form submission error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
