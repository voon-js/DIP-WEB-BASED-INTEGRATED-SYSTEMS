<?php
require '_base.php';

$_SESSION = [];
session_destroy();
header("Location: login.php");
exit();