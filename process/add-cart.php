<?php
include '../config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$productId = mysqli_real_escape_string($conn, $_POST['pid']);
$variantId = mysqli_real_escape_string($conn, $_POST['vid']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = mysqli_real_escape_string($conn, $_POST['price']);
$qty = mysqli_real_escape_string($conn, $_POST['qty']);
$user_id = $_SESSION['id'];

$response = [];

$product_query = "SELECT total_quantity, stock_type FROM products WHERE product_id = '$productId'";
$variant_query = "SELECT variant_quantity, sell_quantity FROM product_variants WHERE variant_id = '$variantId'";

$product_result = mysqli_query($conn, $product_query);
$variant_result = mysqli_query($conn, $variant_query);

if (!$product_result || !$variant_result) {
    $response['status'] = 'error';
    $response['message'] = 'Error fetching product or variant data: ' . mysqli_error($conn);
    echo json_encode($response);
    exit;
}

$product_data = mysqli_fetch_assoc($product_result);
$variant_data = mysqli_fetch_assoc($variant_result);


if ($product_data['stock_type'] == 'multi') {
    $vquantity = $variant_data['variant_quantity'];
    $squantity = $variant_data['sell_quantity'];
    $check_sql = "SELECT cart_qty FROM cart WHERE cart_vid = '$variantId' AND user_id = $user_id";
} else if ($product_data['stock_type'] == 'single') {
    $vquantity = $product_data['total_quantity']; 
    $squantity = $variant_data['sell_quantity'];
    $check_sql = "SELECT SUM(sold_quantity) as total_sold_qty FROM cart WHERE cart_pid = '$productId' AND user_id = $user_id";
}

$check_result = mysqli_query($conn, $check_sql);

if ($product_data['stock_type'] == 'single') {
    $existing_stock = mysqli_fetch_assoc($check_result)['total_sold_qty'] ?? 0;
    $remaining_stock = $vquantity - $existing_stock;

    if (($qty * $squantity) <= $remaining_stock) {
        $variant_check_sql = "SELECT cart_qty FROM cart WHERE cart_vid = '$variantId' AND user_id = $user_id";
        $variant_check_result = mysqli_query($conn, $variant_check_sql);

        if (mysqli_num_rows($variant_check_result) > 0) {
            $existing_item = mysqli_fetch_assoc($variant_check_result);
            $new_qty = $existing_item['cart_qty'] + $qty;
            $subprice = $price * $new_qty;
            $subsellQty = $squantity * $new_qty;

            $update_sql = "UPDATE cart SET cart_qty = $new_qty, cart_subprice = $subprice, sold_quantity = $subsellQty WHERE cart_vid = '$variantId' AND user_id = $user_id";
            $update_result = mysqli_query($conn, $update_sql);

            if (!$update_result) {
                $response['status'] = 'error';
                $response['message'] = 'Error updating cart: ' . mysqli_error($conn);
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Product quantity updated successfully.';
            }
        } else {
            $subprice = $price * $qty;
            $subsellQty = $squantity * $qty;
            $insert_sql = "INSERT INTO cart (cart_vid, cart_pid, cart_name, cart_qty, cart_price, cart_subprice, sold_quantity, user_id) 
                           VALUES ('$variantId','$productId', '$name', $qty, $price, $subprice, $subsellQty, $user_id)";
            $insert_result = mysqli_query($conn, $insert_sql);

            if (!$insert_result) {
                $response['status'] = 'error';
                $response['message'] = 'Error adding to cart: ' . mysqli_error($conn);
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Product added to cart successfully.';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Requested quantity exceeds available stock for ' . $name . '.';
    }
} else {
    if (mysqli_num_rows($check_result) > 0) {
        $existing_item = mysqli_fetch_assoc($check_result);
        $new_qty = $existing_item['cart_qty'] + $qty;

        if (($new_qty * $squantity) <= $vquantity) {
            $subprice = $price * $new_qty;
            $subsellQty = $squantity * $new_qty;
            $update_sql = "UPDATE cart SET cart_qty = $new_qty, cart_subprice = $subprice, sold_quantity = $subsellQty WHERE cart_vid = '$variantId' AND user_id = $user_id";
            $update_result = mysqli_query($conn, $update_sql);

            if (!$update_result) {
                $response['status'] = 'error';
                $response['message'] = 'Error updating cart: ' . mysqli_error($conn);
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Product quantity updated successfully.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Requested quantity exceeds available stock for ' . $name . '.';
        }
    } else {
        if (($qty * $squantity) <= $vquantity) {
            $subprice = $price * $qty;
            $subsellQty = $squantity * $qty;

            $insert_sql = "INSERT INTO cart (cart_vid, cart_pid, cart_name, cart_qty, cart_price, cart_subprice, sold_quantity, user_id) 
                           VALUES ('$variantId','$productId', '$name', $qty, $price, $subprice, $subsellQty, $user_id)";
            $insert_result = mysqli_query($conn, $insert_sql);

            if (!$insert_result) {
                $response['status'] = 'error';
                $response['message'] = 'Error adding to cart: ' . mysqli_error($conn);
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Product added to cart successfully.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Requested quantity exceeds available stock for ' . $name . '.';
        }
    }
}

echo json_encode($response);

?>
