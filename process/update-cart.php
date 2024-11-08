<?php
include '../config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(16));
}

if (isset($_GET['key']) && $_SESSION['key'] == $_GET['key']) {

    $cartId = mysqli_real_escape_string($conn, $_GET['cid']);
    $variantId = mysqli_real_escape_string($conn, $_GET['vid']);
    $newQty = (int) $_GET['qty'];
    $user_id = $_SESSION['id'];
    
    $variantQuery = "SELECT product_id, variant_quantity FROM product_variants WHERE variant_id = '$variantId'";
    $variantResult = mysqli_query($conn, $variantQuery);

    if ($variantResult && mysqli_num_rows($variantResult) > 0) {
        $variantData = mysqli_fetch_assoc($variantResult);
        $productId = $variantData['product_id'];
        $availableQty = $variantData['variant_quantity'];

        $productQuery = "SELECT stock_type, total_quantity FROM products WHERE product_id = '$productId'";
        $productResult = mysqli_query($conn, $productQuery);

        if ($productResult && mysqli_num_rows($productResult) > 0) {
            $productData = mysqli_fetch_assoc($productResult);
            $stockType = $productData['stock_type'];
            $totalQuantity = $productData['total_quantity'];

            if ($stockType === 'multi') {
                $availableQty = $variantData['variant_quantity'];
            } else {
                $availableQty = $totalQuantity;
            }

            if ($newQty <= $availableQty) {
                $updateQuery = "UPDATE cart SET cart_qty = $newQty, cart_subprice = cart_price * $newQty WHERE cart_id = '$cartId' AND user_id = $user_id";
                $updateResult = mysqli_query($conn, $updateQuery);

                if ($updateResult) {
                    $_SESSION['key'] = bin2hex(random_bytes(16));
                    $newKey = $_SESSION['key'];
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cart quantity updated successfully.',
                        'new_key' => $newKey
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database error: ' . mysqli_error($conn)
                    ]);
                }
            } else {
                $_SESSION['key'] = bin2hex(random_bytes(16));
                $newKey = $_SESSION['key'];
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Insufficient stock available.',
                    'new_key' => $newKey
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant not found.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid session key.'
    ]);
}
