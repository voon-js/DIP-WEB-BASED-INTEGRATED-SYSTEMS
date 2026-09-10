<?php
require "database.php";
$_title = "Order History";
include "_head.php";
echo "</header>";
echo "<link rel='stylesheet' href='css/ordHisDet.css'>";
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM orders WHERE user_id = $user_id LIMIT $start_from, $limit";
$result = $conn->query($sql);


echo "<div class='conHistory'>";
echo "<a href='profile.php'>
            <button type='button' class='backbtn'>Back</button>
        </a>";
if ($result->num_rows > 0) {
    echo "<table border='1' class='ordTable'>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                        <th>Details</th>
                        <th>Action</th>
                    </tr>";

    while ($row = $result->fetch_assoc()) {
        $order_time = strtotime($row['order_date']);
        $current_time = time();
        $time_diff = $current_time - $order_time;
        $can_cancel = ($time_diff <= 60) && ($row['order_status'] != 'cancelled');

        echo "<tr data-order-id='{$row['order_id']}' data-order-time='{$order_time}'>
                <td>{$row['order_id']}</td>
                <td>{$row['order_date']}</td>
                <td>RM {$row['total_amount']}</td>
                <td class='order-status'>{$row['order_status']}</td>
                <td><a href='order_detail.php?order_id={$row['order_id']}'><button class='btn'>Detail</button></a></td>
                <td class='cancel-action'>";

        if ($can_cancel) {
            echo "<button class='cancel-btn'>Cancel Order</button>";
        } elseif ($row['order_status'] == 'cancelled') {
            echo "Cancelled";
        } else {
            echo "<button class='cancel-btn disabled'>Cancel Order</button>";
        }

        echo "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No orders found.";
}


echo "<div style='text-align:center; margin-top: 20px;'>";

echo "<form method='GET' style='display:inline-block;'>";



// 页码按钮区
for ($i = 1; $i <= $totalPages; $i++) {
    if ($i == $page) {
        echo "<button type='button' class='page-btn active' disabled>$i</button>";
    } else {
        echo "<button type='submit' name='page' value='$i' class='page-btn'>$i</button>";
    }
}



echo "</form>";
echo "</div>";

echo "</form>";

echo "<script src='js/order_history.js'></script>";
$conn->close();


