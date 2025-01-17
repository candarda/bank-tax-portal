document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Login form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                showError('Bitte füllen Sie alle Pflichtfelder aus.');
                return;
            }
            
            // Submit form
            this.submit();
        });
    }

    // Profile form validation
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form fields
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const birthDate = document.getElementById('birth_date').value;
            
            // Validate required fields
            if (!firstName || !lastName || !email || !phone || !address || !birthDate) {
                showError('Bitte füllen Sie alle Pflichtfelder aus.');
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
                return;
            }
            
            // Validate phone format (simple validation)
            const phoneRegex = /^\+?[0-9\s-]{6,}$/;
            if (!phoneRegex.test(phone)) {
                showError('Bitte geben Sie eine gültige Telefonnummer ein.');
                return;
            }
            
            // Submit form if all validations pass
            this.submit();
        });
    }
});

function showError(message) {
    // Remove existing error messages
    const existingAlert = document.querySelector('.alert-danger');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new error message
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.textContent = message;
    
    // Find the form
    const form = document.querySelector('form');
    if (form) {
        // Insert before form if it's the login form
        if (form.id === 'loginForm') {
            form.parentNode.insertBefore(alert, form);
        } else {
            // Insert after the title for other forms
            const title = document.querySelector('.card-title');
            if (title) {
                title.parentNode.insertBefore(alert, title.nextSibling);
            }
        }
    }
}
