document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // COMMON FUNCTIONALITY (for all admin pages)
    // =============================================
    
    // Auto-dismiss success messages
    const autoDismissMessages = () => {
        const successMessages = document.querySelectorAll('.message.success');
        successMessages.forEach(message => {
            setTimeout(() => {
                message.classList.add('fade-out');
                message.addEventListener('animationend', () => {
                    message.remove();
                });
            }, 3000);
        });
    };
    
    // Initialize for all pages
    autoDismissMessages();
    
    // =============================================
    // ADMIN PROFILE PAGE SPECIFIC FUNCTIONALITY
    // =============================================
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            let isValid = true;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Clear previous errors
            const existingErrors = document.querySelectorAll('.error');
            existingErrors.forEach(error => error.remove());
            
            // Check if passwords match
            if (newPassword !== confirmPassword) {
                const confirmField = document.getElementById('confirm_password');
                const error = document.createElement('span');
                error.className = 'error';
                error.textContent = 'Passwords do not match';
                confirmField.parentNode.appendChild(error);
                isValid = false;
            }
            
            // Check password length
            if (newPassword.length < 6) {
                const newPassField = document.getElementById('new_password');
                const error = document.createElement('span');
                error.className = 'error';
                error.textContent = 'Password must be at least 6 characters';
                newPassField.parentNode.appendChild(error);
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // =============================================
    // MANAGE ORDERS PAGE SPECIFIC FUNCTIONALITY
    // =============================================
    const statusForms = document.querySelectorAll('.status-form');
    if (statusForms.length > 0) {
        // Confirm before cancelling an order
        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const statusSelect = this.querySelector('.status-select');
                if (statusSelect.value === 'cancelled') {
                    if (!confirm('Are you sure you want to cancel this order?')) {
                        e.preventDefault();
                    }
                }
            });
        });
    }
});