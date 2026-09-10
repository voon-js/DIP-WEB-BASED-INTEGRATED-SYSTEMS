<?php
require '_base.php';

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$_title = 'XBURGER LOGIN AND REGISTER PAGE';

// Display any existing messages
$register_success = $_SESSION['register_success'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$login_error = $_SESSION['login_error'] ?? '';
$login_success = $_SESSION['login_success'] ?? '';

// Clear the messages after displaying
unset($_SESSION['register_success'], $_SESSION['register_error'], $_SESSION['login_error'], $_SESSION['login_success']);


//Register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRegister'])) {
    $username = trim($_POST['registerUsername']);
    $email = trim($_POST['registerEmail']);
    $password = $_POST['registerPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $contactNo = $_POST['registercontactNo'];

    // Validate contact number
    if (strlen($contactNo) < 9 || strlen($contactNo) > 14) {
         $_SESSION['register_error'] = 'Contact number must be 9-14 digits (without +60).';
         header("Location: login.php");
         exit();
    }

      // Check if email already exists
      $stmt = $_db->prepare("SELECT email FROM customer WHERE email = ?");
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
          $_SESSION['register_error'] = 'Email already registered.';
          header("Location: login.php");
          exit();
      }
    if ($password !== $confirmPassword) {
        $_SESSION['register_error'] = 'Passwords do not match!';
        header("Location: login.php");
        exit();
    } else {
        // Check password length
        if (strlen($password) < 8) {
            $_SESSION['register_error'] = 'Password must be at least 8 characters';
            header("Location: login.php");
            exit();
        }
        // Check password format
        if (!preg_match('/^[A-Z][a-zA-Z]{2}.{5,}$/', $password)) {
            $_SESSION['register_error'] = 'Password must be 8 characters, first three letters with first uppercase';
            header("Location: login.php");
            exit();
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $_db->prepare("INSERT INTO customer (username, email, contact_no, log_pass) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$username, $email, $contactNo, $password]);

        try {
            if ($success) {
                $_SESSION['register_success'] = 'Registration successful! You can now log in.';
            }
        } catch (PDOException $e) {
            $_SESSION['register_error'] = 'Registration failed. Email may already exist.';
        }
        header("Location: login.php");
        exit();
    }
}

//Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitLogin'])) {
    $email = trim($_POST['loginEmail'] ?? '');
    $password = $_POST['loginPassword'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email and password are required';
        header("Location: login.php");
        exit();
    }

    try {
        $stmt = $_db->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['log_pass'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];

            // 获取并保存用户的头像路径
            $_SESSION['profile_pic'] = $user['profile_pic']; 

            header("Location: home.php");
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid email or password';
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = 'Login error. Please try again.';
        header("Location: login.php");
        exit();
    }
}

?>
<title><?= $_title ?? 'Untitled' ?></title>



<body>
    <main class="loginRegister">
        <link rel="stylesheet" href="/css/login.css">
        <script src="/js/login.js"></script>



        <h2 style="color: aliceblue;">WELCOME TO XBURGER</h2>



        <!--Login Form -->
        <div class="forms" id="login">
            <div class="loginForm">
                <form class="form" id="login" method="post" action="login.php">
                    <div class="user">
                        <p style="color: black; font-size: 50px; font-weight: bolder; font-family: 'Courier New', Courier, monospace;">LOGIN</p><br>
                        <input type="email" id="loginEmail" name="loginEmail" placeholder="Enter your email" required /><br>
                        <br>
                        <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter your password" required /><br>
                        <br>

                        <!-- Display messages -->
                        <?php if ($register_success): ?>
                            <div class="alert success"><?= htmlspecialchars($register_success) ?></div>
                        <?php endif; ?>
                        <?php if ($register_error): ?>
                            <div class="alert error"><?= htmlspecialchars($register_error) ?></div>
                        <?php endif; ?>
                        <?php if ($login_error): ?>
                            <div class="alert error"><?= htmlspecialchars($login_error) ?></div>
                        <?php endif; ?>
                        <?php if ($login_success): ?>
                            <div class="alert success"><?= htmlspecialchars($login_success) ?></div>
                        <?php endif; ?>

                        <button type="submit" id="submitLogin" name="submitLogin" class="button login">Login</button>
                        <p>Don't have an account? <a href="#" id="toRegister">Register here</a></p>
                        <p><a href="forgot.php">Forgot Password?</a></p>
                        <a href="adminLogin.php">
                            <p>Admin Login</p>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Register Form -->
        <div class="forms form-hide " id="register">
            <div class="registerForm">
                <form class="form" id="register" method="post" action="login.php">
                    <div class="user">
                        <p style="color: black; font-size: 50px; font-weight: bolder; font-family: 'Courier New', Courier, monospace;">REGISTER</p><br>

                        <input type="text" id="registerUsername" name="registerUsername" placeholder="Enter username" required /><br><br>

                        <input type="email" id="registerEmail" name="registerEmail" placeholder="Enter your email" required /><br><br>

                        <!-- Inside the register form -->
                        <input type="tel" id="registercontactNo" name="registercontactNo" 
    
                        placeholder="Contact number (9-14 digits, without +60)"
    
                        pattern="[0-9]{9,14}"
    
                        title="Must be 9-14 digits (without +60)"
    
                        required><br><br>

                        <input type="password" id="registerPassword" name="registerPassword" placeholder="Password (min 8 characters e.g:Abc12345)" 
                        pattern="[A-Z][a-zA-Z]{2}.{5,}" 
                        title="Password must be 8 characters. First three letters must be alphabets with the first uppercase." required><br><br>

                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required /><br><br>

                        <button type="submit" name="submitRegister" class="button register">Register</button>

                        <p>Already have an account? <a href="#" class="to-login" id="toLogin">Login here</a></p>
                    </div>
            </div>
            </form>
        </div>
        </div>

    </main>
</body>