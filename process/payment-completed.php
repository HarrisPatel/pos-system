<?php
session_start();
include '../config.php';

$user_id = $_SESSION['id'];
$totalAmount = $_POST['totalAmount'];
$receivedAmount = $_POST['receivedAmount'];
$changeAmount = $receivedAmount - $totalAmount;
$cashier_name = $_SESSION['username'];

$lastReceipt = mysqli_query($conn, "SELECT receipt_no FROM receipts ORDER BY receipt_no DESC LIMIT 1");
if ($lastReceipt && mysqli_num_rows($lastReceipt) > 0) {
    $row = mysqli_fetch_assoc($lastReceipt);
    $lastReceiptNumber = $row['receipt_no'];
    $number = intval(substr($lastReceiptNumber, 2));
    $newNumber = str_pad($number + 1, 5, '0', STR_PAD_LEFT);
    $receiptNo = 'N#' . $newNumber;
} else {
    $receiptNo = 'N#00001';
}

$insertReceiptSql = "INSERT INTO receipts (receipt_no, checkout_time, total_amount, received_amount, change_amount, cashier_name) VALUES ('$receiptNo', NOW(), '$totalAmount', '$receivedAmount', '$changeAmount','$cashier_name')";
mysqli_query($conn, $insertReceiptSql);

$cartSql = "SELECT * FROM cart WHERE user_id = $user_id";
$cartResult = mysqli_query($conn, $cartSql);

if (mysqli_num_rows($cartResult) > 0) {
    while ($row = mysqli_fetch_assoc($cartResult)) {
        $variantId = $row['cart_vid'];
        $variantName = $row['cart_name'];
        $variantQty = $row['cart_qty'];
        $variantPrice = $row['cart_price'];
        $variantSubprice = $row['cart_subprice'];
        $soldQuantity = $row['sold_quantity'];

        $sellSql = "INSERT INTO sales (receipt_no,variant_id, sold_quantity, sale_price, sub_price,sale_date) 
                    VALUES ('$receiptNo', '$variantId', '$variantQty', '$variantPrice', '$variantSubprice',NOW())";

        if (!mysqli_query($conn, $sellSql)) {
            echo "Error inserting into sales: " . mysqli_error($conn);
        }
        $variant_query = "SELECT product_id FROM product_variants WHERE variant_id = '$variantId'";
        $variant_result = mysqli_query($conn, $variant_query);

        if ($variant_result > 0) {
            $fetch = mysqli_fetch_assoc($variant_result);
            $product_id = $fetch['product_id'];
        }

        $product_query = "SELECT stock_type FROM products WHERE product_id = '$product_id'";
        $product_result = mysqli_query($conn, $product_query);
        $fetch1 = mysqli_fetch_assoc($product_result);
        $stock_type = $fetch1['stock_type'];

        // $soldQuantity = $soldQuantity * $variantQty;

        if ($stock_type == 'multi') {
            $updateProductSql = "UPDATE product_variants SET variant_quantity = variant_quantity - $soldQuantity WHERE variant_id = '$variantId'";
        } else if ($stock_type == 'single') {
            $updateProductSql = "UPDATE products SET total_quantity = total_quantity - $soldQuantity WHERE product_id = '$product_id'";
        }
        mysqli_query($conn, $updateProductSql);
    }
} else {
    echo "Cart is empty.";
}

mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
unset($_SESSION['temp-amount']);


header('Location: ../pages/bill-page.php?receipt_no=' . urlencode($receiptNo));
