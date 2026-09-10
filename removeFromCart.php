<!--这是用来删除Cart的商品-->
<?php
require '_base.php';

// Validate input
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
    exit;
}

$product_id = (int)$_POST['product_id'];

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remove item from cart
try {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        echo json_encode(["status" => "success", "message" => "Product removed from cart"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Product not found in cart"]);
    }
} catch(Exception $e) {
    error_log("Cart removal error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Server error"]);
}
