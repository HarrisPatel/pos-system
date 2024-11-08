<?php
include '../config.php';

$storeName = $_POST['storeName'];
$storeAddress = $_POST['storeAddress'];

$sql = "UPDATE store SET storeName='$storeName', storeAddress='$storeAddress'";
if (mysqli_query($conn, $sql)) {
    echo "Product updated successfully";
    header('Location: ../pages/admin-panel.php?option=dashboard');
    exit;
} else {
    echo "Error updating product: " . mysqli_error($conn);
}
