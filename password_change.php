<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // 仅在会话没有启动时调用 session_start()
}
require "database.php";

$_title = "Change Password";
include "_head.php";


?>

<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/passwordChange.css">
    <title>Password Reset</title>
</head>

</header>
<body class="bodyPassChange">

    <main class="mainPassChange">
    <a href="profile.php">
        <button class="btn" style="left: 15%; float: left; position: absolute;">Back</button>
    </a><br>

    <h2>Password Reset</h2>
    

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
    <div class="success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="error"><?php echo $error_message; ?></div>
<?php endif; ?>

    <form method="post">

        <label for="old_password">Current Password:</label>
        <input type="password" id="old_password" name="old_password" required placeholder="Type your current password"><br><br>
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required placeholder="Example password (Abc12345)"><br><br>
        
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your new password"><br><br>

        <input type="submit" name="password_submit" class="btn" value="Change Password">
      
    </form>
    </main>
</body>

</html>