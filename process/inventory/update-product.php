<?php
include '../../config.php';

if (isset($_POST['update-product-id']) && isset($_POST['update-product-name']) && isset($_POST['update-product-category'])  && isset($_POST['update-product-unit']) && isset($_POST['update-product-variant-type'])) {
    $productId = $_POST['update-product-id'];
    $productName = mysqli_real_escape_string($conn, trim($_POST['update-product-name']));
    $productUnit = $_POST['update-product-unit'];
    $productCategory = $_POST['update-product-category'];
    $productStockType = $_POST['update-product-variant-type'];

    $check_sql = "SELECT * FROM products WHERE product_name='$productName' AND category_id='$productCategory' AND product_id != '$productId'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        $sql = "UPDATE products SET product_name='$productName', category_id='$productCategory', unit_id='$productUnit', stock_type='$productStockType' WHERE product_id='$productId'";
        if (mysqli_query($conn, $sql)) {
            echo "Product updated successfully";
            header('Location: ../../pages/admin-panel.php?option=inventory');
            exit;
        } else {
            echo "Error updating product: " . mysqli_error($conn);
        }
    } else {
        echo "Error: A product with the same name already exists in this category.";
    }
} else {
    echo json_encode(['error' => 'Invalid input data']);
}

mysqli_close($conn);
