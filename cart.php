<?php
require '_base.php';


$_title = "CART";
include '_head.php';

$cart = $_SESSION['cart'] ?? [];

?>
</header>

<body>
    <main>
        <div class="container">
            <link rel="stylesheet" href="css/cart.css">

            <div class="cartbox">

                <?php if (empty($cart)): ?>
                    <p class="empty-cart">Your cart is empty</p>
                <?php else: ?>
                    <div class="cart-items">
                        <?php foreach ($cart as $item): ?>
                            <div class="cart-item" data-id="<?= $item['id'] ?>">
                                <div class="item-checkbox">
                                    <input type="checkbox" class="item-select" data-id="<?= $item['id'] ?>" checked>
                                </div>
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
                                    <button class="delete-btn" data-id="<?= $item['id'] ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="cart-total">
                            <p>Total: RM <span id="total-amount"><?= number_format(array_reduce($cart, function ($sum, $item) {
                                                                        return $sum + ($item['price'] * $item['quantity']);
                                                                    }, 0), 2) ?></span></p>
                        </div>
                        <div class="checkout-box">
                            <form id="checkout-form" action="checkout.php" method="POST">
                                <input type="hidden" name="cartData" id="cartData">
                                <button type="submit" id="checkout-btn">Check Out</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="js/cart.js"></script>
</body>

<?php
include '_foot.php';
?>