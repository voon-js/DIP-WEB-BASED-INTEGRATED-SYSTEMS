<?php
// index.php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['staff_id'])){
   header("Location: adminLogin.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XBurger - Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logout-btn {
            background: white;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #bb2d3b;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>XBurger Admin Dashboard</h1>
        <nav class="admin-nav">
    <ul>
        <li><a href="admin_profile.php">Admin Profile</a></li>
        <li><a href="manage_orders.php">Manage Orders</a></li>
        <li><a href="manage_products.php">Manage Products</a></li>
        <li><a href="manage_accounts.php">Manage Accounts</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>
</nav>
    </div>
    <script src="js/script.js"></script>
</body>
</html>