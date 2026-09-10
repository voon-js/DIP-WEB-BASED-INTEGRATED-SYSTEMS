<?php
require_once 'db_connect.php'; // 引入数据库连接

try {
    // 明文密码和对应的员工ID
    $plain_password = '1326';
    $staff_id = 'S003';

    // 生成哈希密码
    $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

    // 更新数据库
    $stmt = $conn->prepare("UPDATE staff SET log_pass = ? WHERE staff_id = ?");
    $stmt->execute([$hashed_password, $staff_id]);

    echo "密码已更新为哈希值！";
} catch (PDOException $e) {
    die("错误：" . $e->getMessage());
}