<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;

    try {
        // Input validation
        if (empty($username) || empty($password)) {
            throw new Exception('Bitte füllen Sie alle Pflichtfelder aus.');
        }

        // Check if username is email
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        
        // Prepare query based on login type
        if ($isEmail) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            throw new Exception('Ungültige Anmeldeinformationen.');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            throw new Exception('Ungültige Anmeldeinformationen.');
        }

        // Check if account is active
        if (!$user['is_active']) {
            throw new Exception('Ihr Konto wurde deaktiviert. Bitte kontaktieren Sie den Support.');
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (86400 * 30); // 30 days
            
            // Store token in database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, date('Y-m-d H:i:s', $expires), $user['id']);
            $stmt->execute();
            
            // Set cookie
            setcookie('remember_token', $token, $expires, '/', '', true, true);
        }

        // Return success response
        echo json_encode([
            'success' => true,
            'redirect' => 'dashboard/'
        ]);

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