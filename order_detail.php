<?php
require "_base.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: order_history.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// 获取订单基本信息
$stmt = $_db->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: order_history.php");
    exit();
}

// 获取订单商品
$stmt = $_db->prepare("
    SELECT oi.*, p.prod_name, p.image 
    FROM order_item oi
    JOIN product p ON oi.product_id = p.prod_id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_title = "Order Details";
include '_head.php';
?>

</header>

<body>
    <main>
        <div class="container">
            <link rel="stylesheet" href="css/cart.css">
            <link rel="stylesheet" href="css/checkout.css">
            <link rel="stylesheet" href="css/ordHisDet.css">

            <div class="cartbox">
                <div class="defaultDet">
                <h2>Order #<?= $order['order_id'] ?></h2>
                <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
                <p><strong>Status:</strong> <?= ucfirst($order['order_status']) ?></p>
                <p><strong>Delivery Option:</strong> <?= ucfirst($order['delivery_option']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                </div>
                
                <div class="cart-items">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="images/<?= $item['image'] ?>" alt="<?= $item['prod_name'] ?>">
                            </div>
                            <div class="item-details">
                                <h3><?= $item['prod_name'] ?></h3>
                                <p>Price: RM <?= number_format($item['price'], 2) ?></p>
                                <p>Quantity: <?= $item['quantity'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="totals-box" style="text-align:right; margin-top: 20px;">
                        <p>Total Item Price: RM <?= number_format(array_reduce($items, function($sum, $item) {
                            return $sum + ($item['price'] * $item['quantity']);
                        }, 0), 2) ?></p>
                        
                        <?php if ($order['delivery_option'] === 'delivery'): ?>
                            <div class="delivery-fee">
                                <p>Delivery Fee: RM 10.00</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="sst-box">
                            <p>SST (6%): RM <?= number_format(array_reduce($items, function($sum, $item) {
                                return $sum + ($item['price'] * $item['quantity']);
                            }, 0) * 0.06, 2) ?></p>
                            <p>Payment Method: 
        <?php 
        switch($order['payment_method']) {
            case 'tng':
                echo "Touch 'n Go";
                break;
            case 'bank':
                echo "Online Bank Transfer";
                break;
            case 'cod':
                echo "Cash on Delivery";
                break;
            default:
                echo ucfirst($order['payment_method']);
        }
        ?>
    </p>
                        </div>
                        
                        <div class="cart-total">
                            <p><strong>Total: RM <?= number_format($order['total_amount'], 2) ?></strong></p>
                            
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="order_history.php"><button class="btn">Back to Order History</button></a>
                </div>
            </div>
        </div>
    </main>
</body>

<?php
include '_foot.php';
?>