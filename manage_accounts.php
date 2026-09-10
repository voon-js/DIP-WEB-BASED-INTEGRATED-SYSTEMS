<?php

session_start();
// manage_accounts.php
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: adminLogin.php");
    exit();
}

// Search functionality - only by user_id
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];

if (!empty($search_term) && is_numeric($search_term)) {
    $where_clause = "WHERE user_id = :user_id";
    $params[':user_id'] = $search_term;
}

// Pagination settings
$customersPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

try {
    // Count total customers
    $count_query = "SELECT COUNT(*) FROM customer $where_clause";
    $count_stmt = $conn->prepare($count_query);
    if (!empty($search_term)) {
        $count_stmt->bindParam(':user_id', $params[':user_id']);
    }
    $count_stmt->execute();
    $totalCustomers = $count_stmt->fetchColumn();
    $totalPages = ceil($totalCustomers / $customersPerPage);
    
    if ($currentPage > $totalPages) $currentPage = $totalPages;
    
    $offset = ($currentPage - 1) * $customersPerPage;
    
    // Get customers
    $query = "SELECT * FROM customer $where_clause ORDER BY user_id DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    if (!empty($search_term)) {
        $stmt->bindParam(':user_id', $params[':user_id']);
    }
    $stmt->bindValue(':limit', $customersPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $customers = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Error fetching customers: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XBurger - Manage Accounts</title>
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
        
        .accounts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .accounts-table th, 
        .accounts-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .accounts-table th {
            background: #f4f4f4;
            font-weight: bold;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-small:hover {
            background: #555;
        }
        
        .account-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .account-details h3 {
            margin-top: 0;
        }
        
        .account-details p {
            margin: 8px 0;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .message.error {
            background: #fdecea;
            border: 1px solid #f5c2c7;
        }
        
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        
        .pagination a, 
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #f4f4f4;
        }
        
        .pagination .current {
            background: #333;
            color: white;
            border-color: #333;
        }
        
        .pagination .disabled {
            color: #ccc;
            pointer-events: none;
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
        <h1>Manage Customer Accounts</h1>
        <a href="admin_index.php" class="back-link">← Back to Dashboard</a>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="search-container">
            <form method="GET" action="manage_accounts.php">
                <input type="number" name="search" placeholder="Search by User ID" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($search_term)): ?>
                    <a href="manage_accounts.php" class="clear-search">Clear search</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="accounts-table-container">
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['age']); ?></td>
                            <td><?php echo htmlspecialchars($customer['gender']); ?></td>
                            <td>
                                <button class="btn-small view-btn" data-id="<?php echo $customer['user_id']; ?>">View</button>
                            </td>
                        </tr>
                        <tr class="details-row" id="details-<?php echo $customer['user_id']; ?>" style="display:none;">
                            <td colspan="6">
                                <div class="account-details">
                                    <h3>Customer Details</h3>
                                    <p><strong>Username:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                                    <p><strong>Age:</strong> <?php echo htmlspecialchars($customer['age']); ?></p>
                                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($customer['gender']); ?></p>
                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($customer['contact_no']); ?></p>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['addresss']); ?></p>
                                    <button class="btn-small close-details" data-id="<?php echo $customer['user_id']; ?>">Close</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=1">&laquo; First</a>
                    <a href="?page=<?php echo $currentPage - 1; ?>">&lsaquo; Prev</a>
                <?php else: ?>
                    <span class="disabled">&laquo; First</span>
                    <span class="disabled">&lsaquo; Prev</span>
                <?php endif; ?>
                
                <?php 
                // Show page numbers (with ellipsis for many pages)
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                
                if ($start > 1) echo '<span>...</span>';
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; 
                
                if ($end < $totalPages) echo '<span>...</span>';
                ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>">Next &rsaquo;</a>
                    <a href="?page=<?php echo $totalPages; ?>">Last &raquo;</a>
                <?php else: ?>
                    <span class="disabled">Next &rsaquo;</span>
                    <span class="disabled">Last &raquo;</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Toggle customer details
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.dataset.id;
                const detailsRow = document.getElementById('details-' + customerId);
                
                // Hide all other details first
                document.querySelectorAll('.details-row').forEach(row => {
                    if (row.id !== 'details-' + customerId) {
                        row.style.display = 'none';
                    }
                });
                
                // Toggle current details
                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                } else {
                    detailsRow.style.display = 'none';
                }
            });
        });
        
        // Close details
        document.querySelectorAll('.close-details').forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.dataset.id;
                document.getElementById('details-' + customerId).style.display = 'none';
            });
        });
    </script>
</body>
</html>