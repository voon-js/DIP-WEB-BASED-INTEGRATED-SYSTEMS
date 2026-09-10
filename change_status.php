<?php
require "database.php";

$order_id = $_GET['order_id'];
?>

<a href="manage_orders.php">
        <button>Back</button>
    </a><br><br>

<form method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order_id?>">

    <label for="delivering">Delivering</label><br>
    <input type="radio" id="delivering" name="status" value="delivering"><br><br>

    <label for="delivered">Delivered</label><br>
    <input type="radio" id="delivered" name="status" value="delivered"><br><br>

    <label for="cancelled">Cancelled</label><br>
    <input type="radio" id="cancelled" name="status" value="cancelled"><br><br>

    <input type="submit" name="status_submit" value="Submit">
</form>