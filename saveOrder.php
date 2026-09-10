<?php
require '_base.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['orderData'])) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request']));
}

$orderData = json_decode($_POST['orderData'], true);
$user_id = $_SESSION['user_id'];

try {
    // 开始事务
    $_db->beginTransaction();

    // 1. 插入订单主表
    $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : 'unknown';

$stmt = $_db->prepare("
    INSERT INTO orders (user_id, order_date, total_amount, order_status, delivery_option, address, payment_method)
    VALUES (?, NOW(), ?, 'processing', ?, ?, ?)
");
$stmt->execute([
    $user_id,
    $orderData['total'],
    $orderData['delivery_option'],
    $orderData['address'],
    $paymentMethod
]);
    $order_id = $_db->lastInsertId();

    // 2. 插入订单项
    foreach ($orderData['items'] as $item) {
        $stmt = $_db->prepare("
            INSERT INTO order_item (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // 3. 清空购物车
    if (isset($_POST['paidItems']) && isset($_SESSION['cart'])) {
        $paidItems = $_POST['paidItems'];
    
        // 如果是string，尝试转回数组
        if (is_string($paidItems)) {
            $paidItems = explode(',', $paidItems);
        }
    
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($paidItems) {
            return !in_array($item['id'], $paidItems);
        });
    }

    // 提交事务
    $_db->commit();

    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
} catch (PDOException $e) {
    // 回滚事务
    $_db->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>