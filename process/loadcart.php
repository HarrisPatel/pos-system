<?php
include '../config.php';
session_start();

var_dump($_POST);

$index = 1;
$cartHtml = '';
$totalAmount = 0;
$GetTotal = 0;

// if (!isset($_SESSION['key'])) {
//     $_SESSION['key'] = bin2hex(random_bytes(16));
// }


// if (isset($_POST['key']) && $_SESSION['key'] === $_POST['key']) {
    $check_sql1 = "SELECT cart.*, product_variants.variant_id, product_variants.product_id
                   FROM cart 
                   JOIN product_variants ON cart.cart_pid = product_variants.variant_id";
    $result1 = mysqli_query($conn, $check_sql1);

    if (!$result1) {
        error_log("Database query error: " . mysqli_error($conn));
        $response['status'] = 'error';
        $response['message'] = 'Error fetching cart: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $product_id = $row['product_id'];
            $variant_id = $row['variant_id'];
            $cartId = $row['cart_id'];
            $cartName = $row['cart_name'];
            $cartQty = $row['cart_qty'];
            $cartPrice = $row['cart_price'];
            $cartSubprice = $row['cart_subprice'];
            $availableQuantity = $row['product_quantity'];

            $GetTotal += $cartSubprice;
            $rate = mysqli_query($conn, "SELECT SUM(tax_rate) AS total_rate FROM taxes");
            $rateRow = mysqli_fetch_assoc($rate);
            $total_rate = $rateRow['total_rate'] ?? 0; 

            $temptotalAmount = $GetTotal + ($GetTotal * ($total_rate / 100));

            $cartHtml .= '
                <tr>
                    <td>' . $index . '</td> 
                    <td>' . htmlspecialchars($cartName) . '</td>
                    <td> <input class="qty-update-input w-100" style="border:none;outline:none" type="number" value ="' . $cartQty . '" required data-vid="' . $row['variant_id'] . '" data-cart-id="' . $cartId . '"/></td>
                    <td>Rs.' . $cartPrice . '</td>
                    <td>Rs.' . $cartSubprice . '</td>
                    <td><button class="delete-cart btn btn-danger btn-sm float-start" data-cart-id="' . $cartId . '">Delete</button></td>
                </tr>';

            $index++;
        }
        $total_Amount = '<h4 style="margin : 0;width:100%;text-align:left;border:none;">Total Amount</h4><h3><b style="float:left">Rs.' . $temptotalAmount . '</b></h3>';
        $_SESSION['temp-amount'] = $temptotalAmount;
        $_SESSION['get-total'] = $GetTotal;
    } else {
        $_SESSION['temp-amount'] = 0;
        $_SESSION['get-total'] = 0;
        $GetTotal = 0;
        $total_Amount = '<h4 style="margin : 0;width:100%;text-align:left;border:none;">Total Amount</h4><h3><b style="float:left">Rs.0</b></h3>';
        $cartHtml = '<tr><td colspan="6" class="text-center">No items in cart</td></tr>';
    }

    $response['status'] = 'success';
    $response['cartHtml'] = $cartHtml;
    $response['totalAmount'] = $total_Amount;
    $response['getTotal'] = $GetTotal;
// } else {
    // $response['status'] = 'error';
    // $response['message'] = 'Invalid session key.';
// }

// $_SESSION['key'] = bin2hex(random_bytes(16));
// $response['new_key'] = $_SESSION['key'];

ob_end_clean();
echo json_encode($response);
