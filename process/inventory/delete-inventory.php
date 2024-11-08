<?php

include '../../config.php';


$pid = $_GET['pid'];

$sql = "DELETE FROM products WHERE product_id = $pid";
$result = mysqli_query($conn, $sql);

header('Location: ../../pages/admin-panel.php?option=inventory');
