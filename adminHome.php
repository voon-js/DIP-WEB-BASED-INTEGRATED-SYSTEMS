<?php 
require '_base.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$_title ="HOME PAGE";
include '_head.php';
?>

