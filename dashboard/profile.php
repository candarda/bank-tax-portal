<?php
session_start();
require_once '../includes/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Kullanıcı bilgilerini getir
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $birth_date = $_POST['birth_date'];
    $billing_address = trim($_POST['billing_address']);

    try {
        // Email değişikliği varsa, başka kullanıcıda kullanılıyor mu kontrol et
        if ($email !== $user['email']) {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->bind_param("si", $email, $_SESSION['user_id']);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception('Diese E-Mail-Adresse wird bereits verwendet.');
            }
        }

        // Bilgileri güncelle
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, birth_date = ?, billing_address = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $address, $birth_date, $billing_address, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = 'Ihre Daten wurden erfolgreich aktualisiert.';
        } else {
            throw new Exception('Beim Aktualisieren Ihrer Daten ist ein Fehler aufgetreten.');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil bearbeiten - Mein Vodafone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-page">
    <!-- Header -->
    <header class="dashboard-header">
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="../assets/images/vodafone-logo.png" alt="Vodafone" height="40">
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="profile.php">Mein Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="billing.php">Rechnungen</a>
                        </li>
                    </ul>
                    
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profil bearbeiten</a></li>
                            <li><a class="dropdown-item" href="settings.php">Einstellungen</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Abmelden</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title mb-4">Persönliche Daten</h2>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" id="profileForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Vorname</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nachname</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-Mail</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Telefonnummer</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($user['address']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Geburtsdatum</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                       value="<?php echo htmlspecialchars($user['birth_date']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="billing_address" class="form-label">Rechnungsadresse</label>
                                <textarea class="form-control" id="billing_address" name="billing_address" 
                                          rows="3"><?php echo htmlspecialchars($user['billing_address']); ?></textarea>
                                <div class="form-text">Nur ausfüllen, wenn die Rechnungsadresse von der Wohnadresse abweicht.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Änderungen speichern</button>
                                <a href="index.php" class="btn btn-outline-secondary">Abbrechen</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
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
    <script src="../assets/js/main.js"></script>
</body>
</html> 