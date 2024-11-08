<?php

include '../../config.php';
session_start();

$taxName = mysqli_real_escape_string($conn, trim($_POST['tax-name']));
$taxRate = $_POST['tax-rate'];

if (!empty($taxName)) {

    $check_sql = "SELECT * FROM taxes WHERE tax_name = '{$taxName}'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        header('Location: ../../pages/admin-panel.php?option=tax&error=tax-name');
        exit();
    } else {
        if ($taxRate <= 100) {
            $sql = "INSERT INTO taxes (tax_name, tax_rate, tax_time) VALUES ('{$taxName}', '{$taxRate}', NOW())";
            if (mysqli_query($conn, $sql)) {
                header('Location: ../../pages/admin-panel.php?option=tax');
            }
        } else {
            header('Location: ../../pages/admin-panel.php?option=tax&error=tax-rate');
        }
    }
} else {
    header('Location: ../../pages/admin-panel.php?option=tax');
}
