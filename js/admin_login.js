// Remove any code that might interfere with admin login form
document.addEventListener("DOMContentLoaded", function () {
    // Only run this code on admin login page
    if (document.getElementById('adminLogin')) {
        const form = document.getElementById('adminLogin');
        
        form.addEventListener('submit', function(e) {
            const staffId = document.getElementById('staff_id')?.value;
            const password = document.getElementById('password')?.value;
            
            if (!staffId || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    }
});