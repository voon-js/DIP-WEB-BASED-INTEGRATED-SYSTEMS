<?php
session_start();

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 数据库连接
$host = 'localhost'; // 通常是localhost
$db = 'xburger'; // 你的数据库名
$user = 'root'; // 通常是root（如果有密码记得加）
$pass = ''; // 你的数据库密码（如果有）

$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 从session拿user_id
$user_id = $_SESSION['user_id'];

// 查找用户资料
$sql = "SELECT * FROM customer WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $email = $user['email'];
    $profile_pic = $user['profile_pic'] ?: '/images/default-profile.png';
    $age = $user['age'];
    $gender = $user['gender'];
    $contact_no = $user['contact_no'];
    $addresss = $user['addresss']; // 注意：你的字段名在DB是`addresss`，有3个s
} else {
    echo "User not found.";
    exit();
}

$stmt->close();
$conn->close();

// 页面标题
$_title = "User Profile";
include '_head.php'; // 如果你有需要引入共用head文件
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/profile.css">
    <script src="js/profile.js"></script>
</head>
<body>
</header>
    <div class="profile-container">
        <div class="sidebar">
            <ul>
                <li><button onclick="personalDetails()" id="personalDetails" class="filter">Personal Details</button></li>
                <li><button onclick="changePassword()" id="changePassword" class="filter">
                    <a href="password_change.php" class="overPage">Change Password</a></button></li>
                <li><button onclick="openHistory()" id="openHistory" class="filter">
                <a href="order_history.php" class="overPage">Order History</a></button></li>
            </ul>
        </div>

        <div class="profile-details">
        <div class="box">
            <img src="<?= $profile_pic ?>" alt="Profile Picture" width="150">

            <p>Email: <?= htmlspecialchars($email) ?></p>
            <p>Username: <?= htmlspecialchars($username) ?></p>
            <p>Phone Number: +60<?= htmlspecialchars($contact_no) ?></p>
            <p>Address: <?= htmlspecialchars($addresss) ?></p>


            <!-- 这个是会跳去那个member_profile，最好做成按钮 -->
            <button>
            <a href="member_profile.php" class="overPage" class="overPageBtn">Edit Profile</a>
            </button>
        </div>
        </div>
    </div>
</body>
</html>

