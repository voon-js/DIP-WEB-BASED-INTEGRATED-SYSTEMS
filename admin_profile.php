<?php
// admin_profile.php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['staff_id'])) {
   header("Location: adminLogin.php");
   exit();
}

// Get admin data
$staff_id = $_SESSION['staff_id'];
$query = "SELECT * FROM staff WHERE staff_id = :staff_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':staff_id', $staff_id);
$stmt->execute();
$admin = $stmt->fetch();

// Handle password change
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $admin['log_pass'])) {
        if ($new_password === $confirm_password) {
            // Validate new password requirements
            if (strlen($new_password) < 8 || !preg_match('/^[A-Z][a-zA-Z]{2}/', $new_password)) {
                $message = "Password must be at least 8 characters with first 3 letters (1st uppercase)";
            } else {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                try {
                    $update_query = "UPDATE staff SET log_pass = :new_password WHERE staff_id = :staff_id";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bindParam(':new_password', $hashed_password);
                    $update_stmt->bindParam(':staff_id', $staff_id);
                    
                    if ($update_stmt->execute()) {
                        $message = "Password changed successfully!";
                        // Update local session data
                        $admin['log_pass'] = $hashed_password;
                    } else {
                        $message = "Error updating password.";
                    }
                } catch (PDOException $e) {
                    $message = "Database error: " . $e->getMessage();
                }
            }
        } else {
            $message = "New passwords don't match!";
        }
    } else {
        $message = "Current password is incorrect!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XBurger - Admin Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Admin Profile</h1>
        <a href="admin_index.php" class="back-link">← Back to Dashboard</a>
        
        <div class="profile-section">
            <div class="profile-info">
                <h2>Your Information</h2>
                <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($admin['staff_id']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['staff_name']); ?></p>
                <p><strong>Birth Date:</strong> <?php echo htmlspecialchars($admin['birth_date']); ?></p>
                <p><strong>Hire Date:</strong> <?php echo htmlspecialchars($admin['hire_date']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            </div>
            
            <div class="password-change">
                <h2>Change Password</h2>
                <?php if ($message): ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php endif; ?>
                <form id="passwordForm" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn">Change Password</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>