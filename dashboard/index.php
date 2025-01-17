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
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mein Vodafone</title>
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
                            <a class="nav-link active" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Mein Profil</a>
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
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Willkommen zurück, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>
                        <p class="card-text">Hier finden Sie eine Übersicht Ihrer Vodafone Services und Aktivitäten.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-user-edit text-primary me-2"></i>
                            Persönliche Daten
                        </h5>
                        <p class="card-text">Aktualisieren Sie Ihre persönlichen Informationen und Kontaktdaten.</p>
                        <a href="profile.php" class="btn btn-primary">Bearbeiten</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-invoice text-success me-2"></i>
                            Rechnungen
                        </h5>
                        <p class="card-text">Sehen Sie Ihre aktuellen und vergangenen Rechnungen ein.</p>
                        <a href="billing.php" class="btn btn-primary">Anzeigen</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-headset text-info me-2"></i>
                            Support
                        </h5>
                        <p class="card-text">Benötigen Sie Hilfe? Kontaktieren Sie unseren Kundenservice.</p>
                        <a href="support.php" class="btn btn-primary">Hilfe erhalten</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Letzte Aktivitäten</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Aktivität</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('d.m.Y H:i'); ?></td>
                                        <td>Letzte Anmeldung</td>
                                        <td><span class="badge bg-success">Erfolgreich</span></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo date('d.m.Y', strtotime('-1 day')); ?></td>
                                        <td>Profilaktualisierung</td>
                                        <td><span class="badge bg-info">Abgeschlossen</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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