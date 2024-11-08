<?php
include '../../config.php';

$pid = $_POST['product_id'];
$ptype = $_POST['product_type'];
$vname = mysqli_real_escape_string($conn, trim($_POST['variant_name']));
$vprice = $_POST['variant_price'];
$vsell = $_POST['variant_sell'];

if ($ptype == 'single') {
    if (!empty($vname)) {
        $check_sql = "SELECT * FROM product_variants WHERE product_id = $pid AND variant_name = '$vname'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) == 0) {
            $sql = "INSERT INTO product_variants (product_id, variant_name, variant_quantity, variant_price, created_at, sell_quantity) VALUES ($pid, '$vname', 0, $vprice, NOW(), $vsell)";

            if (mysqli_query($conn, $sql)) {
                header('Location: ../../pages/admin-panel.php?option=stock-manage&&pid=' . $pid);
            } else {
                echo "Error: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Variant with the same name already exists.<br>";
        }
    } else {
        header('Location: ../../pages/admin-panel.php?option=stock-manage&&pid=' . $pid);
    }
} elseif ($ptype == 'multi' && !empty($vname)) {
    if (!empty($vname)) {
        $vquanity = $_POST['variant_quantity'] ?? 0;

        $check_sql = "SELECT * FROM product_variants WHERE product_id = $pid AND variant_name = '$vname'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) == 0) {
            $sql = "INSERT INTO product_variants (product_id, variant_name, variant_quantity, variant_price, created_at, sell_quantity) VALUES ($pid, '$vname', $vquanity, $vprice, NOW(), $vsell)";

            if (mysqli_query($conn, $sql)) {
                header('Location: ../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid);
            } else {
                echo "Error: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Variant with the same name already exists.<br>";
        }
    } else {
        header('Location: ../../pages/admin-panel.php?option=multi-stock-manage&&pid=' . $pid);
    }
}

mysqli_close($conn);
?>
