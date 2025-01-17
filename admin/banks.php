<?php
session_start();
require_once '../includes/db.php';

// Oturum kontrolü
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Banka ekleme
if (isset($_POST['add_bank'])) {
    $name = trim($_POST['bank_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO banks (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success = 'Bank wurde erfolgreich hinzugefügt.';
        } else {
            $error = 'Fehler beim Hinzufügen der Bank.';
        }
    }
}

// Banka güncelleme
if (isset($_POST['update_bank'])) {
    $id = (int)$_POST['bank_id'];
    $name = trim($_POST['bank_name']);
    $active = isset($_POST['bank_active']) ? 1 : 0;
    
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE banks SET name = ?, active = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $active, $id);
        if ($stmt->execute()) {
            $success = 'Bank wurde erfolgreich aktualisiert.';
        } else {
            $error = 'Fehler beim Aktualisieren der Bank.';
        }
    }
}

// Banka silme
if (isset($_POST['delete_bank'])) {
    $id = (int)$_POST['bank_id'];
    
    // Önce bankanın kullanımda olup olmadığını kontrol et
    $check = $conn->prepare("SELECT COUNT(*) as count FROM customer_data WHERE bank_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error = 'Diese Bank kann nicht gelöscht werden, da sie von Kunden verwendet wird.';
    } else {
        $stmt = $conn->prepare("DELETE FROM banks WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = 'Bank wurde erfolgreich gelöscht.';
        } else {
            $error = 'Fehler beim Löschen der Bank.';
        }
    }
}

// Bankaları getir
$banks = $conn->query("SELECT * FROM banks ORDER BY name");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banken verwalten - Vodafone Admin</title>
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
                        <a class="nav-link" href="index.php">Kunden</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="banks.php">Banken</a>
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
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Banken</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
                            <i class="fas fa-plus"></i> Neue Bank
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="banksTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Erstellt am</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($bank = $banks->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $bank['id']; ?></td>
                                            <td><?php echo htmlspecialchars($bank['name']); ?></td>
                                            <td>
                                                <?php if ($bank['active']): ?>
                                                    <span class="badge bg-success">Aktiv</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inaktiv</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($bank['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-bank" 
                                                        data-id="<?php echo $bank['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($bank['name']); ?>"
                                                        data-active="<?php echo $bank['active']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-bank" 
                                                        data-id="<?php echo $bank['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($bank['name']); ?>">
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

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Neue Bank hinzufügen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Name der Bank</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" name="add_bank" class="btn btn-primary">Hinzufügen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Bank Modal -->
    <div class="modal fade" id="editBankModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Bank bearbeiten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="bank_id" id="edit_bank_id">
                        <div class="mb-3">
                            <label for="edit_bank_name" class="form-label">Name der Bank</label>
                            <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="edit_bank_active" name="bank_active">
                                <label class="form-check-label" for="edit_bank_active">Aktiv</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" name="update_bank" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Bank Modal -->
    <div class="modal fade" id="deleteBankModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Bank löschen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="bank_id" id="delete_bank_id">
                        <p>Möchten Sie die Bank <strong id="delete_bank_name"></strong> wirklich löschen?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" name="delete_bank" class="btn btn-danger">Löschen</button>
                    </div>
                </form>
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
            $('#banksTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
                },
                order: [[0, 'desc']]
            });

            // Edit bank
            $('.edit-bank').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const active = $(this).data('active');

                $('#edit_bank_id').val(id);
                $('#edit_bank_name').val(name);
                $('#edit_bank_active').prop('checked', active == 1);

                new bootstrap.Modal($('#editBankModal')).show();
            });

            // Delete bank
            $('.delete-bank').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#delete_bank_id').val(id);
                $('#delete_bank_name').text(name);

                new bootstrap.Modal($('#deleteBankModal')).show();
            });
        });
    </script>
</body>
</html> 