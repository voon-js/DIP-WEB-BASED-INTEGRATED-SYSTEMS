<!--付款后会删除Cart的商品-->

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paidItems'])) {
    $paidItems = $_POST['paidItems']; 
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($paidItems) {
            return !in_array($item['id'], $paidItems);
        });
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
