<?php

include '../../config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(16));
}

if (!isset($_POST['key'])) {
    $response['status'] = 'error';
    $response['message'] = 'Session key is missing.';
    echo json_encode($response);
    exit; 
}



if (isset($_POST['key']) && $_SESSION['key'] == $_POST['key']) {

    $vid = mysqli_real_escape_string($conn, $_POST['vid']); 

    $delete_sql = "DELETE FROM return_cart WHERE variant_id = $vid";
    $delete_result = mysqli_query($conn, $delete_sql);

    $response['status'] = 'success';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid session key.';
}
$_SESSION['key'] = bin2hex(random_bytes(16));
$response['new_key'] = $_SESSION['key']; 

echo json_encode($response);
?>
