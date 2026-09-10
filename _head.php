<?php
// if (isset($_SESSION['user_id']))
?>
<DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $_title ?? 'Untitled' ?></title>
        <link rel="shortcut icon" href="/images/favicon.png" type="image/png">
        <link rel="stylesheet" href="/css/app.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="/js/app.js"></script>
    </head>

    <body>
        <header style="display: flex;">
            <h1 style="text-align: center; color:rgba(14, 120, 242, 0.968)">
                <a href="home.php">
                    XBURGER
                </a>
            </h1>
            <nav class="head">
                <a href="home.php">Home</a>
                <a href="aboutUs.php">About Us</a>
            </nav>

            <nav class="navRight">
                <a href="cart.php"><img src="/images/cart.png"></a>
                <!-- Auth Buttons -->
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Logged in state -->
                        <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                        <a href="logout.php" class="auth-button">Logout</a>

                        <!-- 显示用户头像 -->
                        <?php if (isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic'])): ?>
                            <a href="profile.php"><img src="<?= $_SESSION['profile_pic'] ?>?v=<?= time() ?>" alt="Profile Picture"></a>
                        <?php else: ?>
                            <a href="profile.php"><img src="/images/profile.png?<?= time() ?>" alt="Profile Picture"></a>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Logged out state -->
                        <a href="login.php" class="auth-button">Login</a>
                    <?php endif; ?>
                </div>
            </nav>
            </div>
        </header>


        <header class="title">
            <main>
                <h1><?= $_title ?? 'Untitled' ?></h1>