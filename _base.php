<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}    
date_default_timezone_set('Asia/Kuala_Lumpur');

$_db = new PDO('mysql:dbname=xburger', 'root', '', [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,]);
