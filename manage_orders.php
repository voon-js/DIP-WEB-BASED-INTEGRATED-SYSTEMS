<?php
// manage_orders.php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: adminLogin.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['order_status'];
        
        try {
            $update_query = "UPDATE orders SET order_status = :status WHERE order_id = :order_id";
            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            
            $message = "Order status updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating order status: " . $e->getMessage();
        }
    }
}

// Get search term if exists
$search_term = $_GET['search'] ?? '';
$where_clause = '';
$params = [];

if (!empty($search_term)) {
    $where_clause = "WHERE o.order_id LIKE :search_term";
    $params[':search_term'] = "%$search_term%";
}

// Get ALL orders from the orders table
try {
    $query = "
    SELECT 
        o.order_id, 
        o.order_date, 
        o.total_amount, 
        o.order_status,
        c.username, 
        c.email,
        GROUP_CONCAT(oi.product_id) AS product_ids  -- 联表获取产品ID
    FROM orders o
    LEFT JOIN customer c ON o.user_id = c.user_id
    LEFT JOIN order_item oi ON o.order_id = oi.order_id  -- 新增联表
    $where_clause
    GROUP BY o.order_id  -- 按订单分组
    ORDER BY o.order_date DESC
";
              
    $stmt = $conn->prepare($query);
    
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->execute();
    $orders = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $orders = [];
    $error = "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XBurger - Manage Orders</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .no-customer {
            color: #999;
            font-style: italic;
        }
        .search-container {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .search-container input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex-grow: 1;
            max-width: 300px;
        }
        .search-container button {
            padding: 8px 15px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-container button:hover {
            background: #555;
        }
        .clear-search {
            margin-left: 10px;
            color: #666;
            text-decoration: none;
        }
        .clear-search:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Orders</h1>
        <a href="admin_index.php" class="back-link">← Back to Dashboard</a>
        
        <?php if (isset($message)): ?>
            <div class="message success slide-in"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="search-container">
            <form method="GET" action="manage_orders.php">
                <input type="text" name="search" placeholder="Search by Order ID" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($search_term)): ?>
                    <a href="manage_orders.php" class="clear-search">Clear search</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="orders-table">
            <?php if (empty($orders)): ?>
                <div class="message info">
                    <?php echo empty($search_term) ? 'No orders found in the database.' : 'No orders match your search.'; ?>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product IDs</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td>
                                <?php if (!empty($order['username'])): ?>
                                    <?php echo htmlspecialchars($order['username']); ?>
                                    <small><?php echo htmlspecialchars($order['email']); ?></small>
                                <?php else: ?>
                                    <span class="no-customer">Customer not found</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['product_ids'] ?? 'N/A'); ?></td>                            
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="order_status" class="status-select">
                                        <option value="preparing" <?php echo $order['order_status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="prepared" <?php echo $order['order_status'] === 'prepared' ? 'selected' : ''; ?>>Prepared</option>
                                        <option value="delivering" <?php echo $order['order_status'] === 'delivering' ? 'selected' : ''; ?>>Delivering</option>
                                        <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-small">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>