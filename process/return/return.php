<?php
session_start();
include '../../config.php';

$totalAmount = $_POST['totalAmount'];
$receiptNo = $_POST['receiptNO'];

if ($totalAmount !== 0) {
    $cartSql = "SELECT * FROM return_cart";
    $cartResult = mysqli_query($conn, $cartSql);

    if (mysqli_num_rows($cartResult) > 0) {
        while ($row = mysqli_fetch_assoc($cartResult)) {
            $productId = $row['product_id'];
            $variantId = $row['variant_id'];
            $saleId = $row['sale_id'];
            $quantity = $row['quantity'];
            $price = $row['price'];
            $subprice = $row['subprice'];
            $reason = $row['reason'];

            $checkSql = "SELECT * FROM return_items WHERE reason = '$reason' AND variant_id = '$variantId' AND receipt_no = '$receiptNo'";
            $checkResult = mysqli_query($conn, $checkSql);

            if (mysqli_num_rows($checkResult) > 0) {
                $existingRow = mysqli_fetch_assoc($checkResult);
                $newQuantity = $existingRow['quantity'] + $quantity;
                $newSubprice = $existingRow['sub_price'] + $subprice;

                $updateReturnSql = "UPDATE return_items 
                                    SET quantity = '$newQuantity', sub_price = '$newSubprice'
                                    WHERE variant_id = '$variantId' AND receipt_no = '$receiptNo'";

                if (!mysqli_query($conn, $updateReturnSql)) {
                    echo "Error updating return_items: " . mysqli_error($conn);
                    exit();
                }
            } else {
                $returnSql = "INSERT INTO return_items (variant_id, quantity, price, sub_price, date, receipt_no,reason) 
                              VALUES ('$variantId', '$quantity', '$price', '$subprice', NOW(), '$receiptNo','$reason')";

                if (!mysqli_query($conn, $returnSql)) {
                    echo "Error inserting into return_items: " . mysqli_error($conn);
                    exit();
                }
            }

            if ($reason !== 'expire' && $reason !== 'defective') {
                $variant_query = "SELECT product_id, sell_quantity FROM product_variants WHERE variant_id = '$variantId'";
                $variant_result = mysqli_query($conn, $variant_query);

                if (mysqli_num_rows($variant_result) > 0) {
                    $fetch = mysqli_fetch_assoc($variant_result);
                    $product_id = $fetch['product_id'];
                    $returnQty = $fetch['sell_quantity'];
                }

                $product_query = "SELECT stock_type FROM products WHERE product_id = '$product_id'";
                $product_result = mysqli_query($conn, $product_query);
                $fetch1 = mysqli_fetch_assoc($product_result);
                $stock_type = $fetch1['stock_type'];

                $returnQty = $returnQty * $quantity;

                if ($stock_type == 'multi') {
                    $updateProductSql = "UPDATE product_variants SET variant_quantity = variant_quantity + $returnQty WHERE variant_id = '$variantId'";
                } else if ($stock_type == 'single') {
                    $updateProductSql = "UPDATE products SET total_quantity = total_quantity + $returnQty WHERE product_id = '$product_id'";
                }
                mysqli_query($conn, $updateProductSql);
            }
        }
    } else {
        echo "Cart is empty.";
        exit();
    }
}

mysqli_query($conn, "DELETE FROM return_cart");

header('Location: ../../pages/return-confirm.php');
exit();
