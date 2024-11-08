<?php
include '../../config.php';

$pname = mysqli_real_escape_string($conn, trim($_POST['product-name']));  
$pcategory = $_POST['product-category'];
$pvariant_type = $_POST['product-varient-type'];
$punit = $_POST['product-unit'];

if (!empty($pname)) {
    $check_sql = "SELECT * FROM products WHERE product_name = '$pname' AND category_id = '$pcategory'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        $sql = "INSERT INTO products (product_name, category_id, unit_id, total_quantity, stock_type) VALUES ('$pname', '$pcategory', $punit, 0, '$pvariant_type')";
        mysqli_query($conn, $sql);
    }
}

mysqli_close($conn);
header('Location: ../../pages/admin-panel.php?option=inventory');
?>
