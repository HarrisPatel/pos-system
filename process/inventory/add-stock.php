<?php

include '../../config.php';



$pid = $_POST['pid'];
if (isset($_POST['vid'])) {
    $vid = $_POST['vid'];
}
$newStock  = $_POST['add-stock'];

$sql = "SELECT * FROM products WHERE product_id = $pid";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {

    $stype = $row['stock_type'];

    if ($stype == 'single') {
        $total = $row['total_quantity'] + $newStock;

        $sql1 = "UPDATE products SET total_quantity = {$total} WHERE product_id = $pid";
        $result1 = mysqli_query($conn, $sql1);
        header('Location: ../../pages/admin-panel.php?option=stock-manage&&pid=' . $pid);
    } else if ($stype == 'multi') {

        $sql1 = "SELECT * FROM product_variants WHERE variant_id = $vid";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $total = $row1['variant_quantity'] + $newStock;

        $sql2 = "UPDATE product_variants SET variant_quantity = {$total} WHERE variant_id = $vid";
        $result2 = mysqli_query($conn, $sql2);
        header('Location: ../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid);
    }
} else {
    echo "Invalid product ID or stock value.";
}
