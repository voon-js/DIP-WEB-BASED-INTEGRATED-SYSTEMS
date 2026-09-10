<?php
require '_base.php';

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$forgot_error = $_SESSION['forgot_error'] ?? '';

unset($_SESSION['forgot_error']);

// // Forgot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitForgot'])) {
    $email = trim($_POST['forgotEmail']);
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmNewPassword'];

    // Validate password
    if (strlen($newPassword) < 8) {
        $_SESSION['forgot_error'] = 'Password must be at least 8 characters';
        header("Location: forgot.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['forgot_error'] = 'Passwords do not match';
        header("Location: forgot.php");
        exit();
    }

    // Check password format
    if (!preg_match('/^[A-Z][a-zA-Z]{2}.{5,}$/', $newPassword)) {
        $_SESSION['forgot_error'] = 'Password must be 8 characters, first three letters with first uppercase';
        header("Location: forgot.php");
        exit();
    }

    try {
        // Check if email exists
        $stmt = $_db->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['forgot_error'] = 'Email not found';
            header("Location: forgot.php");
            exit();
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $_db->prepare("UPDATE customer SET log_pass = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        $_SESSION['login_success'] = 'Password updated successfully!';
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['forgot_error'] = 'Error updating password';
        header("Location: forgot.php");
        exit();
    }
}

?>

<body>
    <main class="loginRegister">
        <link rel="stylesheet" href="/css/login.css">
        <script src="/js/login.js"></script>

        <h2 style="color: aliceblue;">WELCOME TO XBURGER</h2>

        <!--Forgot Form-->
        <div class="forms" id="forgot">
            <div class="forgotForm">
                <form method="POST" action="forgot.php">
                    <div class="user">
                        <p style="color: black; font-size: 50px; font-weight: bolder; font-family: 'Courier New', Courier, monospace;">RESET PASSWORD</p>
                        <input type="email" name="forgotEmail" placeholder="Enter your email" required><br><br>
                        <input type="password" name="newPassword" placeholder="New Password (min 8 characters e.g:Abc12345)" required><br><br>
                        <input type="password" name="confirmNewPassword" placeholder="Confirm New Password" required><br><br>
                        <!-- Display error -->
                        <?php if ($forgot_error): ?>
                            <div class="alert error"><?= htmlspecialchars($forgot_error) ?></div>
                        <?php endif; ?>
                        <button type="submit" name="submitForgot" class="button">Reset Password</button>
                        <a href="login.php">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>