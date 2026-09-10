<?php

session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

// Search functionality
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];

if (!empty($search_term) && is_numeric($search_term)) {
    $where_clause = "WHERE prod_id = :prod_id";
    $params[':prod_id'] = $search_term;
}

function handleImageUpload($file, $productName) {
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    
    $uploadDir = 'images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = strtolower(str_replace(' ', '_', $productName)) . '.' . $ext;
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    return '';
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_product'])) {
            $imageName = handleImageUpload($_FILES['product_image'], $_POST['prod_name']);
            
            $stmt = $conn->prepare("INSERT INTO product (prod_name, price, descrip, prod_cat, product_status, images) 
                                  VALUES (:name, :price, :desc, :cat, :product_status, :image)");
            $stmt->execute([
                ':name' => $_POST['prod_name'],
                ':price' => $_POST['price'],
                ':desc' => $_POST['descrip'],
                ':cat' => $_POST['prod_cat'],
                ':product_status' => $_POST['product_status'],
                ':image' => $imageName ?: 'default.jpg'
            ]);
            $message = "Product added successfully!";
            
        } elseif (isset($_POST['update_product'])) {
            $imageName = $_POST['current_image'];
            
            if (!empty($_FILES['product_image']['name'])) {
                $newImage = handleImageUpload($_FILES['product_image'], $_POST['prod_name']);
                if ($newImage) $imageName = $newImage;
            }
            
            $stmt = $conn->prepare("UPDATE product SET 
                                  prod_name = :name,
                                  price = :price,
                                  descrip = :desc,
                                  prod_cat = :cat,
                                  product_status = :product_status,
                                  image = :image
                                  WHERE prod_id = :id");
            $stmt->execute([
                ':name' => $_POST['prod_name'],
                ':price' => $_POST['price'],
                ':desc' => $_POST['descrip'],
                ':cat' => $_POST['prod_cat'],
                ':product_status' => $_POST['product_status'],
                ':image' => $imageName,
                ':id' => $_POST['prod_id']
            ]);
            $message = "Product updated successfully!";
            
        } elseif (isset($_POST['delete_product'])) {
            $stmt = $conn->prepare("DELETE FROM product WHERE prod_id = :id");
            $stmt->execute([':id' => $_POST['prod_id']]);
            $message = "Product deleted successfully!";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

$products = $conn->query("SELECT * FROM product ORDER BY prod_id DESC")->fetchAll();
$categories = ['Burger', 'Side', 'Drink'];
$availabilityOptions = ['In Stock', 'Low Stock', 'Out of Stock', 'Discontinued'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XBurger - Manage Products</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }

        .product-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-row {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-row label {
            min-width: 120px;
        }

        .form-actions {
            margin-top: 15px;
        }

        .products-table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th, 
        .products-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .products-table th {
            background: #f4f4f4;
            font-weight: bold;
        }

        .edit-form {
            background: #f0f8ff;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .no-image {
            color: #999;
            font-style: italic;
            font-size: 0.9em;
        }

        .products-table img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 3px;
        }

        .btn {
            padding: 8px 15px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #555;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .message.success {
            background: #e6f7e6;
            border: 1px solid #a1d8a1;
        }

        .message.error {
            background: #fdecea;
            border: 1px solid #f5c2c7;
        }

        .file-upload {
            margin-top: 10px;
        }

        .slide-in {
            animation: slideIn 0.3s ease forwards;
        }

        .fade-out {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .availability-in-stock {
            color: #4CAF50;
            font-weight: bold;
        }
        .availability-low-stock {
            color: #FF9800;
            font-weight: bold;
        }
        .availability-out-of-stock {
            color: #F44336;
            font-weight: bold;
        }
        .availability-discontinued {
            color: #9E9E9E;
            font-weight: bold;
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
        <h1>Manage Products</h1>
        <a href="admin_index.php" class="back-link">← Back to Dashboard</a>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Error') === false ? 'success slide-in' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.querySelector('.message');
                    if (msg) msg.classList.add('fade-out');
                }, 3000);
            </script>
        <?php endif; ?>

        <div class="search-container">
            <form method="GET" action="manage_products.php">
                <input type="number" name="search" placeholder="Search by Product ID" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($search_term)): ?>
                    <a href="manage_products.php" class="clear-search">Clear search</a>
                <?php endif; ?>
            </form>
        </div>
        
       
        <div class="product-form">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="prod_name">Product Name:</label>
                    <input type="text" id="prod_name" name="prod_name" required>
                </div>
                <div class="form-row">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-row">
                    <label for="descrip">Description:</label>
                    <input type="text" id="descrip" name="descrip">
                </div>
                <div class="form-row">
                    <label for="prod_cat">Category:</label>
                    <select id="prod_cat" name="prod_cat" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
    <label for="product_status">Product Status:</label>
    <select id="product_status" name="product_status" required>
        <option value="on">On</option>
        <option value="off">Off</option>
    </select>
</div>
                <div class="form-row">
                    <label>Product Image:</label>
                    <input type="file" name="product_image" class="file-upload" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" name="add_product" class="btn">Add Product</button>
                </div>
            </form>
        </div>
        
        
        <div class="products-table">
            <h2>Current Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Product Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['prod_id']); ?></td>
                        <td><?php echo htmlspecialchars($product['prod_name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php 
                            $image_path = 'images/' . htmlspecialchars($product['image']);
                            if (file_exists($image_path) && !empty($product['image'])): 
                            ?>
                                <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['prod_name']); ?>">
                            <?php else: ?>
                                <span class="no-image">Image not found</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['prod_cat']); ?></td>
                        <td class="product_status-<?php echo strtolower(str_replace(' ', '-', $product['product_status'])); ?>">
                            <?php echo htmlspecialchars($product['product_status']); ?>
                        </td>
                        <td>
                            <button class="btn-small edit-btn" data-id="<?php echo $product['prod_id']; ?>">Edit</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="prod_id" value="<?php echo $product['prod_id']; ?>">
                                <button type="submit" name="delete_product" class="btn-small delete-btn" 
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr class="edit-form" id="edit-form-<?php echo $product['prod_id']; ?>" style="display:none;">
                        <td colspan="7">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="prod_id" value="<?php echo $product['prod_id']; ?>">
                                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                                <div class="form-row">
                                    <label>Name:</label>
                                    <input type="text" name="prod_name" value="<?php echo htmlspecialchars($product['prod_name']); ?>" required>
                                </div>
                                <div class="form-row">
                                    <label>Price:</label>
                                    <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                </div>
                                <div class="form-row">
                                    <label>Description:</label>
                                    <input type="text" name="descrip" value="<?php echo htmlspecialchars($product['descrip']); ?>">
                                </div>
                                <div class="form-row">
                                    <label>Category:</label>
                                    <select name="prod_cat" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat; ?>" <?php echo $product['prod_cat'] === $cat ? 'selected' : ''; ?>>
                                                <?php echo $cat; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-row">
    <label>Status:</label>
    <select name="product_status" required>
        <option value="on" <?php echo $product['product_status'] === 'on' ? 'selected' : ''; ?>>On</option>
        <option value="off" <?php echo $product['product_status'] === 'off' ? 'selected' : ''; ?>>Off</option>
    </select>
</div>
                                <div class="form-row">
                                    <label>Product Image:</label>
                                    <?php if (!empty($product['image']) && file_exists('images/' . $product['image'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" width="50">
                                    <?php endif; ?>
                                    <input type="file" name="product_image" class="file-upload" accept="image/*">
                                    <small>Leave empty to keep current image</small>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="update_product" class="btn-small">Update</button>
                                    <button type="button" class="btn-small cancel-edit">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
      
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const formId = 'edit-form-' + this.dataset.id;
                document.getElementById(formId).style.display = 'table-row';
            });
        });
        
        
        document.querySelectorAll('.cancel-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.edit-form').style.display = 'none';
            });
        });
    </script>
</body>
</html>