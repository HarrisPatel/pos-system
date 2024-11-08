<?php
include '../../config.php';

$pid = $_POST['product_id'];
$vid = $_POST['variant_id'];
$ptype = $_POST['product_type'];
$vname = mysqli_real_escape_string($conn, trim($_POST['variant_name']));
$vprice = $_POST['variant_price'];
$vsell = $_POST['variant_sell'];

echo $vid;

if (!empty($vname)) {
    $check_sql = "SELECT * FROM product_variants WHERE product_id = $pid AND variant_name = '$vname' AND variant_id != $vid";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        if ($ptype == 'single') {
            $sql = "UPDATE product_variants SET variant_name='$vname', variant_quantity=0, variant_price=$vprice, sell_quantity=$vsell WHERE variant_id = $vid";
            $redirect_url = '../../pages/admin-panel.php?option=stock-manage&&pid=' . $pid;
        } else {
            $sql = "UPDATE product_variants SET variant_name='$vname', variant_price=$vprice, sell_quantity=$vsell WHERE variant_id = $vid";
            $redirect_url = '../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid;
        }

        if (mysqli_query($conn, $sql)) {
            echo "Query executed successfully!<br>";
            header('Location: ' . $redirect_url);
        } else {
            echo "Error: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error: A variant with the same name already exists for this product.";
    }
} else {
    header('Location: ../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid);
}

mysqli_close($conn);
