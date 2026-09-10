<?php
require '_base.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取当前用户的地址
$user_id = $_SESSION['user_id'];
$stmt = $_db->prepare("SELECT addresss FROM customer WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_address = $user['addresss'] ?? ''; // 如果地址为空，默认为空字符串

$checkOut = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cartData'])) {
    $checkOut = json_decode($_POST['cartData'], true);
}

$shopaddress = [
    ['address' => 'No44,SS98']
];

$_title = "CheckOut";
include '_head.php';

?>
</header>

<body>
    <main>
        <div class="container">
            <link rel="stylesheet" href="css/cart.css">
            <link rel="stylesheet" href="css/checkout.css">


            <div class="cartbox">
                <div class="delivery-options">
                    <button id="btn-delivery">Delivery</button>
                    <!-- 用来输出店面地址 -->
                    <button id="btn-pickup" data-address="<?= $shopaddress[0]['address'] ?>">Pick Up</button>

                </div>

                <div id="address-box" class="address-box" style="display:none;" data-user-address="<?= htmlspecialchars($user_address) ?>">
    <input type="text" id="delivery-address" placeholder="Enter your delivery address" value="<?= htmlspecialchars($user_address) ?>">
    <button id="save-address-btn" class="confirm-btn">Save Address</button>
</div>

                <!-- 用来输出商品 -->
                <div class="cart-items">
                    <?php foreach ($checkOut as $item): ?>
                        <div class="cart-item" data-id="<?= $item['id'] ?>">
                            <div class="item-image">
                                <img src="images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                            </div>
                            <div class="item-details">
                                <h3><?= $item['name'] ?></h3>
                                <p>ID: <?= $item['id'] ?></p>
                                <p>Price: RM <?= number_format($item['price'], 2) ?></p>
                                <div class="quantity-control">
                                    <button class="quantity-btn minus" data-id="<?= $item['id'] ?>">-</button>
                                    <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" max="99" data-id="<?= $item['id'] ?>">
                                    <button class="quantity-btn plus" data-id="<?= $item['id'] ?>">+</button>
                                </div>
                                <p class="subtotal">Subtotal: RM <span><?= number_format($item['price'] * $item['quantity'], 2) ?></span></p>
                            </div>
                            <div class="item-right">
                                <!-- 只剩下1个就不能删除 -->
                                <button class="delete-btn" data-id="<?= $item['id'] ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- 用来输出Delivery那些资料 -->
                    <div class="totals-box" style="text-align:right; margin-top: 20px;">
                        <p>Total Item Price: RM <span id="item-total">0.00</span></p>
                        <div class="delivery-fee" id="delivery-fee-box" style="display:none;">
                            <p>Delivery Fee: RM <span id="delivery-fee">10.00</span></p>
                        </div>
                        <div class="sst-box">
                            <p>SST (6%): RM <span id="sst-amount">0.00</span></p>
                        </div>
                        <div class="cart-total">
                            <p><strong>Total: RM <span id="total-amount">...</span></strong></p>
                        </div>
                    </div>
                    <div class="checkout-box">
                        <button id="checkout-btn">Payment</button>
                    </div>
                </div>

            </div>
        </div>
    </main>


    <!-- 付款按钮 -->
    <div id="paymentModal" class="model">
        <div class="modelcontent">
            <span class="close" onclick="closePaymentModal()" style="position:absolute;top:10px;right:15px;cursor:pointer;font-size:30px">&times;</span>
            <div class="box2">
                <h2>Select Payment Method</h2>
                <label>
                    <input type="radio" name="payment_method" value="tng" required>
                    Touch 'n Go
                </label><br><br>
                <label>
                    <input type="radio" name="payment_method" value="bank">
                    Online Bank Transfer
                </label><br><br>
                <label>
                    <input type="radio" name="payment_method" value="cod">
                    Cash on Delivery
                </label><br><br>

                <button id="confirmPaymentBtn" class="addCart">Confirm Payment</button>
            </div>
            
        </div>
    </div>

    <!-- TNG QR 弹窗 -->
    <div id="tngQRModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); text-align:center; padding-top:10%;">
        <div style="background:#fff; padding:40px; border-radius:10px; display:inline-block; position:relative;">
        <!-- 关闭按钮 -->
        <button onclick="closeTngQR()" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:30px; cursor:pointer;">&times;</button>

        <h3>Please scan to pay with Touch 'n Go</h3>
        <img src="images/QR.jpg" alt="TNG QR" style="max-width:300px; margin:20px 0;">
        <br>
        <button id="tngConfirmBtn" class="confirm-btn">Confirm</button>
        </div>
    </div>


    <!-- Payment Processing 弹窗 -->
    <div id="processingModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); text-align:center; padding-top:20%;">
        <div style="background:#fff; padding:20px; border-radius:10px; display:inline-block;">
            <h2>Payment is being processed...</h2>
        </div>
    </div>

    <!-- Payment Complete 弹窗 -->
    <div id="completeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); text-align:center; padding-top:20%;">
        <div style="background:#fff; padding:20px; border-radius:10px; display:inline-block;">
            <h2>Payment Complete</h2>
            <p>Check Your Order at User Profile</p>
            <button id="completeConfirmBtn" class="confirm-btn" style="padding: 8px 16px; margin-top:10px;">Confirm</button>
        </div>
    </div>

    <script src="js/checkOut.js"></script>
</body>

<script>
function closeTngQR() {
    document.getElementById('tngQRModal').style.display = 'none';
}
</script>

<?php
include '_foot.php';
?>