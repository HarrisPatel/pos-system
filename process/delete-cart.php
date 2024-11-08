<?php

include '../config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');



$cartId = mysqli_real_escape_string($conn, $_POST['cart_id']);
$userid = $_SESSION['id'];

$delete_sql = "DELETE FROM cart WHERE cart_id = $cartId AND user_id =  $userid";
$delete_result = mysqli_query($conn, $delete_sql);

$response['status'] = 'success';

echo json_encode($response);
