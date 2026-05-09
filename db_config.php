<?php

$host = 'localhost';
$user = 'dainikni_user_libmnage';
$pass = '(?&exw0(lat_';
$db = 'dainikni_rman_lib_manage';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Function to generate a unique order number
function generateOrderNumber() {
    return 'ORD' . date('YmdHis') . rand(100, 999);
}
