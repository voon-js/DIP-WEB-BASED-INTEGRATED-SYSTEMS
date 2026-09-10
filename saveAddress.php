<?php
require '_base.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'error' => 'Not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['address'])) {
    die(json_encode(['success' => false, 'error' => 'Invalid request']));
}

$address = trim($_POST['address']);
$user_id = $_SESSION['user_id'];

// 地址长度验证
if (strlen($address) < 5) {
    die(json_encode(['success' => false, 'error' => 'Address is too short']));
}

try {
    $stmt = $_db->prepare("UPDATE customer SET addresss = ? WHERE user_id = ?");
    $stmt->execute([$address, $user_id]);
    
    // 返回成功响应
    echo json_encode(['success' => true]);
} catch (PDOException $e) {

    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>