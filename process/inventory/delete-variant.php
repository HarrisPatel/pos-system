<?php

include '../../config.php';


$vid = $_GET['vid'];
$pid = $_GET['pid'];


$sql = "SELECT stock_type FROM products WHERE product_id = $pid";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$sql = "DELETE FROM product_variants WHERE variant_id = $vid";
$result = mysqli_query($conn, $sql);


$stype = $row['stock_type'];

if ($stype == 'single') {
    header('Location: ../../pages/admin-panel.php?option=stock-manage&&pid=' . $pid);
    
}else if ($stype == 'multi') {
    header('Location: ../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid);
    
}



