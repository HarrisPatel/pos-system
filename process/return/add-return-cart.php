<?php
include '../../config.php';
session_start();

ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$rno = mysqli_real_escape_string($conn, $_POST['rno']);
$vid = mysqli_real_escape_string($conn, $_POST['vid']);
$pid = mysqli_real_escape_string($conn, $_POST['pid']);
$sid = mysqli_real_escape_string($conn, $_POST['sid']);
$price = mysqli_real_escape_string($conn, $_POST['price']);
$qty = mysqli_real_escape_string($conn, $_POST['qty']);
$reason = mysqli_real_escape_string($conn, $_POST['reason']);
$userId = $_SESSION['id'];

if (!isset($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(16));
}

$response = [];

if (isset($_POST['key']) && $_SESSION['key'] == $_POST['key']) {

    $sale_sql = "SELECT * FROM sales WHERE sale_id = '$sid'";
    $sale_result = mysqli_query($conn, $sale_sql);
    $sale_fetch = mysqli_fetch_assoc($sale_result);
    
    if (!$sale_result) {
        $response['status'] = 'error';
        $response['message'] = 'Error fetching sales data: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    $checkSql = "SELECT SUM(quantity) as returned_qty FROM return_items WHERE variant_id = '$vid' AND receipt_no = '$rno'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (!$checkResult) {
        $response['status'] = 'error';
        $response['message'] = 'Error fetching return items: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    $check_fetch = mysqli_fetch_assoc($checkResult);
    $returned_qty = $check_fetch['returned_qty'] ?? 0;

    $remaining_qty = $sale_fetch['variant_qty'] - $returned_qty;

    if ($qty > $remaining_qty) {
        $response['status'] = 'error';
        $response['message'] = 'Requested quantity exceeds available stock. Only ' . $remaining_qty . ' items can be returned.';
        echo json_encode($response);
        exit;
    }

    $check_cart_sql = "SELECT * FROM return_cart WHERE variant_id = '$vid' AND user_id = $userId";
    $check_cart_result = mysqli_query($conn, $check_cart_sql);
    
    if (!$check_cart_result) {
        $response['status'] = 'error';
        $response['message'] = 'Database error: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($check_cart_result) > 0) {
        $existing_item = mysqli_fetch_assoc($check_cart_result);
        $new_qty = $existing_item['quantity'] + $qty;

        if ($new_qty <= $remaining_qty) {
            $subprice = $price * $new_qty;
            $update_sql = "UPDATE return_cart SET quantity = $new_qty, subprice = $subprice, reason = '$reason' WHERE variant_id = '$vid' AND user_id = $userId";
            $update_result = mysqli_query($conn, $update_sql);

            if (!$update_result) {
                $response['status'] = 'error';
                $response['message'] = 'Database error during update: ' . mysqli_error($conn);
            } else {
                $_SESSION['key'] = bin2hex(random_bytes(16));  
                $response['status'] = 'success';
                $response['message'] = 'Product quantity updated successfully.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Requested quantity exceeds available stock.';
        }
    } else {
        if ($qty <= $remaining_qty) {
            $subprice = $price * $qty;
            $insert_sql = "INSERT INTO return_cart (product_id,variant_id,user_id,sale_id, quantity, price, subprice, reason) VALUES ('$pid','$vid','$userId','$sid','$qty','$price','$subprice', '$reason')";
            $insert_result = mysqli_query($conn, $insert_sql);

            if (!$insert_result) {
                $response['status'] = 'error';
                $response['message'] = 'Database error during insert: ' . mysqli_error($conn);
            } else {
                $_SESSION['key'] = bin2hex(random_bytes(16));  
                $response['status'] = 'success';
                $response['message'] = 'Product added to cart successfully.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Requested quantity exceeds available stock.';
        }
    }


} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid session key.';
    
}
$response['new_key'] = $_SESSION['key'];

echo json_encode($response);
?>
