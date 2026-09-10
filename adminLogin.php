<?php
require '_base.php';
require_once 'db_connect.php';

if (isset($_SESSION['staff_id'])) {
    header("Location: admin_index.php");
    exit();
}

$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitLogin'])) {
    $staff_id = trim($_POST['staff_id'] ?? '');
    $password = $_POST['password'] ?? '';

    
    if (empty($staff_id) || empty($password)) {
        $_SESSION['login_error'] = 'Please fill in all fields';
        header("Location: adminLogin.php");
        exit();
    }

    try {
        $stmt = $_db->prepare("SELECT staff_id, log_pass FROM staff WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff && password_verify($password, $staff['log_pass'])) {
            $_SESSION['staff_id'] = $staff['staff_id'];
            header("Location: admin_index.php");
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid credentials';
            header("Location: adminLogin.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = 'Login error. Please try again.';
        header("Location: adminLogin.php");
        exit();
    }
}

$_title = 'XBURGER ADMIN LOGIN';
?>
<title><?= $_title ?? 'Untitled' ?></title>

<body>


    <style>

    </style>

    <main class="loginRegister">
        <link rel="stylesheet" href="/css/login.css">
        <script src="/js/admin_login.js"></script>

        <h2 style="color: aliceblue;">WELCOME TO XBURGER</h2>

        <!-- Admin Login Form -->
        <div class="forms" id="login">
            <div class="loginForm">
                <form class="form" id="adminLogin" method="POST" action="adminLogin.php">
                    <div class="user">
                        <p style="color: black; font-size: 50px; font-weight: bolder; font-family: 'Courier New', Courier, monospace;">ADMIN LOGIN</p><br>
                        <input type="text" id="staff_id" name="staff_id" placeholder="Staff ID" required><br><br>
                        <input type="password" id="password" name="password" placeholder="Password" required><br><br>
                        <?php if ($login_error): ?>
                            <div class="alert error"><?= htmlspecialchars($login_error) ?></div>
                        <?php endif; ?>
                        <button type="submit" name="submitLogin" class="button login">Login</button>
                        <p>Back to user login? <a href="login.php">Click here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>