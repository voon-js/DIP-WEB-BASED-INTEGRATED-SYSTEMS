<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
require "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    
    // Check if the order is within 1 minute
    $stmt = $conn->prepare("SELECT order_date FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    $order_time = strtotime($order['order_date']);
$current_time = time();
$time_diff = $current_time - $order_time;
    
    if ($time_diff <= 60) {
        // Update order status to cancelled
        $update = $conn->prepare("UPDATE orders SET order_status = 'cancelled' WHERE order_id = ?");
        $update->bind_param("i", $order_id);
        
        if ($update->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Time limit (1min) exceeded']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>