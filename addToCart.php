<?php
require '_base.php';

$product_id = $_POST['prod_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// 如果产品不存在就 return
if (!isset($_POST['prod_id']) || !is_numeric($_POST['prod_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
    exit;
}

// Fetch product details from database
try {
    $stmt = $_db->prepare("SELECT * FROM product WHERE prod_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error"]);
    exit;
}

// 初始化购物车
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 如果已经有了，就增加数量
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'id' => $product_id,
        'name' => $product['prod_name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'quantity' => $quantity
    ];
}

echo json_encode(["status" => "success", "message" => "Added Succesfully"]);