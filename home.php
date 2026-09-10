<?php
require '_base.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$_title = "HOME PAGE";
include '_head.php';

try {
    $stmt = $_db->prepare("SELECT * FROM product");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}

?>

<!-- In your navigation -->
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.php">Login</a>
<?php endif; ?>



<h1> Welcome to XBURGER </h1>
</header>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search">
                <button id="searchButton">Search</button>
            </div>
            <ul>
                <li><button onclick="showAll()" class="filter">All</button></li>
                <li><button onclick="showBurger()" class="filter">Burger</button></li>
                <li><button onclick="showSide()" class="filter">Side</button></li>
                <li><button onclick="showDrink()" class="filter">Drink</button></li>
                <li><button onclick="sortHighPrice()" class="filter">High Price</button></li>
<li><button onclick="sortLowPrice()" class="filter">Low Price</button></li>
            </ul>
        </div>

        <main>
            <div class="container">
                <link rel="stylesheet" href="css/home.css">
                <script src="js/home.js"></script>


                <!-- 生成产品 -->
                <div class="menu">
                    <?php if (empty($products)): ?>
                        <p>No products available at the moment.</p>
                    <?php else: ?>
                        <!-- 显示能用商品 -->
                        <?php foreach ($products as $product): ?>
                            <?php if ($product['product_status'] === 'on'): ?>
                                <div class="box" data-type="<?= htmlspecialchars($product['prod_cat']) ?>">
                                    <div class="<?= str_replace(' ', '', htmlspecialchars($product['prod_name'])) ?>">
                                        <img src="images/<?= htmlspecialchars($product['image']) ?>"
                                            onclick="openModel('<?= htmlspecialchars($product['prod_cat'] . $product['prod_id']) ?>')">
                                        <p><?= htmlspecialchars($product['prod_name']) ?></p>
                                        <p>RM <?= number_format($product['price'], 2) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <!-- 显示停用商品 -->
                        <?php foreach ($products as $product): ?>
                            <?php if ($product['product_status'] === 'off'): ?>
                                <div class="box disabled" data-type="<?= htmlspecialchars($product['prod_cat']) ?>">
                                    <div class="<?= str_replace(' ', '', htmlspecialchars($product['prod_name'])) ?>">
                                        <img src="images/<?= htmlspecialchars($product['image']) ?>" style="filter: grayscale(100%);">
                                        <p><?= htmlspecialchars($product['prod_name']) ?> (Unavailable)</p>
                                        <p>RM <?= number_format($product['price'], 2) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Product modals -->
                <?php foreach ($products as $product): ?>
                    <?php if ($product['product_status'] === 'on'): ?>
                        <div id="<?= htmlspecialchars($product['prod_cat'] . $product['prod_id']) ?>Modal" class="model">
                            <div class="modelcontent">
                                <span class="close" onclick="closeModel('<?= htmlspecialchars($product['prod_cat'] . $product['prod_id']) ?>')">×</span>
                                <div class="box2">
                                    <img src="images/<?= htmlspecialchars($product['image']) ?>">
                                    <p><?= htmlspecialchars($product['prod_name']) ?></p><br>
                                    <p><?= htmlspecialchars($product['descrip']) ?></p>
                                    <p>RM <?= number_format($product['price'], 2) ?></p>
                                    <div class="quantity-wrapper">
                                        <button class="qty-btn minus" onclick="changeQty(<?= $product['prod_id'] ?>, -1)">-</button>
                                        <input type="number" id="qty<?= $product['prod_id'] ?>" name="quantity" min="1" max="99" value="1">
                                        <button class="qty-btn plus" onclick="changeQty(<?= $product['prod_id'] ?>, 1)">+</button>
                                    </div>

                                    <div class="popupBottom">
                                        <button onclick="addToCart(<?= $product['prod_id'] ?>)" class="addCart">Add To Cart</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>


<?php include '_foot.php'; ?>