<?php
include '../../config.php';
session_start();

$cartHtml = '';
$GetTotal = 0;

// if (!isset($_SESSION['key'])) {
//     $_SESSION['key'] = bin2hex(random_bytes(16));
// }

$response = [];

// if (isset($_GET['key']) && $_SESSION['key'] === $_GET['key']) {
    // $_SESSION['key'] = bin2hex(random_bytes(16));

    $check_sql1 = "SELECT return_cart.*, sales.sale_id, sales.sold_quantity AS sold_quantity FROM return_cart JOIN sales ON return_cart.sale_id = sales.sale_id";
    $result1 = mysqli_query($conn, $check_sql1);

    if (!$result1) {
        $response['status'] = 'error';
        $response['message'] = 'Error fetching cart: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $vid = $row['variant_id'];
            $productQty = $row['quantity'];
            $productPrice = $row['price'];
            $productSubprice = $row['subprice'];
            $productreason = $row['reason'];
            $availableQuantity = $row['sold_quantity'];
            $GetTotal += $productSubprice;

            $rate = mysqli_query($conn, "SELECT SUM(tax_rate) AS total_rate FROM taxes");
            
            $rateRow = mysqli_fetch_assoc($rate);
            $total_rate = $rateRow['total_rate'];

            $temptotalAmount = $GetTotal + ($GetTotal * ($total_rate / 100));

            $name_sql  = "SELECT variant_name FROM product_variants WHERE variant_id = $vid";
            $result_name = mysqli_query($conn,$name_sql);
            $fetch_name = mysqli_fetch_assoc($result_name);
            $name = $fetch_name['variant_name'];

            $cartHtml .= '
            <tr>
                <td>' . htmlspecialchars($name) . '</td>
                <td>' . $productQty . '</td>
                <td>Rs.' . $productPrice . '</td>
                <td>Rs.' . $productSubprice . '</td>
                <td>' . $productreason . '</td>
                <td><button class="delete-cart btn btn-danger btn-sm" data-vid="' . $vid . '">delete</button></td>
            </tr>
        ';
        
        }

        $response['return_cartHtml'] = $cartHtml;
        $response['status'] = 'success';
        $response['getTotal'] = $GetTotal;
        $response['totalAmount'] = $temptotalAmount;
    } else {
        $response['status'] = 'success';
        $response['return_cartHtml'] = '';
        $response['getTotal'] = 0;
        $response['totalAmount'] = 0;
    }
    $response['new_key'] = $_SESSION['key'];

// } else {
//     $response['status'] = 'error';
//     $response['message'] = 'Session key mismatch.';
// }

echo json_encode($response);
?>
