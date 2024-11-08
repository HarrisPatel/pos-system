<?php
include '../config.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'cashier') {
    header('Location: ../index.php');
    exit();
}

// if (!isset($_GET['key'])) {
//     $response['status'] = 'error';
//     $response['message'] = 'Session key is missing.';
//     echo json_encode($response);
//     exit();
// }

// if (!isset($_SESSION['key'])) {
//     $_SESSION['key'] = bin2hex(random_bytes(16));
// }

// error_log("Session Key: " . $_SESSION['key']);
// error_log("GET Key: " . ($_GET['key'] ?? 'No key in GET'));

// if ($_SESSION['key'] === $_GET['key']) {

    if (isset($_GET['query'])) {
        $query = mysqli_real_escape_string($conn, $_GET['query']);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Missing query parameter.';
        echo json_encode($response);
        exit();
    }

    if ($query !== '') {
        $sql = "SELECT * FROM product_variants WHERE variant_name LIKE '%$query%' OR variant_id LIKE '%$query%'";
    } else {
        $sql = "SELECT * FROM product_variants";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $itemsHtml = '';

        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $variantId = $row['variant_id'];
            $variantName = $row['variant_name'];
            $variantQty = $row['variant_quantity'];
            $variantPrice = $row['variant_price'];

            $itemsHtml .= '
            <div class="item-box">
                <div>
                    <p class="m-0" style="font-size : 13px">Product id: ' . $variantId . '</p>
                    <h5 class="m-0" style="font-size : 18px;width:180px">' . htmlspecialchars($variantName) . '</h5>
                    ';
                    $productSql = "SELECT total_quantity,stock_type FROM products WHERE product_id = $productId";
                    $productResult = mysqli_query($conn,$productSql);
                    $row2 = mysqli_fetch_assoc($productResult);
                    if($row2['stock_type'] == 'multi'){
                        $itemsHtml .= '<p class="m-0" style="font-size : 14px"><b>stock: ' . $variantQty . ' | Rs. ' . $variantPrice . '</b></p>';
                    }else{
                        $itemsHtml .= '<p class="m-0" style="font-size : 14px"><b>stock: ' . $row2['total_quantity'] . ' | Rs. ' . $variantPrice . '</b></p>';

                    }
            $itemsHtml .=    '</div>
                <div style="width:60px">
                    <input class="qty-input" type="number" value="1" style="width:100%"/>
                    <button class="add-btn btn btn-primary btn-sm w-100 mt-1" 
                        data-pid="' . $productId . '" 
                        data-vid="' . $variantId . '" 
                        data-name="' . htmlspecialchars($variantName) . '" 
                        data-price="' . $variantPrice . '">Add</button>
                </div>
            </div>';
        }

        $response['status'] = 'success';
        $response['itemsHtml'] = $itemsHtml;
        $response['done'] = "done";
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error executing query: ' . mysqli_error($conn);
    }
// } else {
//     $response['status'] = 'error';
//     $response['message'] = 'Invalid session key.';
// }

// $_SESSION['key'] = bin2hex(random_bytes(16));
// $response['new_key'] = $_SESSION['key'];

echo json_encode($response);
