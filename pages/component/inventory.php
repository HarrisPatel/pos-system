<?php
include '../config.php';

if (isset($_GET['uid'])) {
    $update_id = $_GET['uid'];
}

$fetch_sql = "SELECT * FROM products ORDER BY product_id DESC;";
$result = mysqli_query($conn, $fetch_sql);

$sql = "SELECT cid, cname FROM category";
$result1 = mysqli_query($conn, $sql);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h1">Inventory</h1>
</div>
<div class="row">
    <form class="mt-3 col-md-6" id="add-product" action=".././process/inventory/add-new-product.php" method="POST">
        <input type="text" class="mt-3 mb-1  border border-2 border-dark p-1" name="product-name" placeholder="Product Name" required style="outline: none;width:200px;">

        <select class="mt-3 border border-2 border-dark p-1" name="product-category" placeholder="Select Category" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:200px">
            <option value="" disabled selected>Select Category</option>
            <?php
            if (mysqli_num_rows($result1) > 0) {
                while ($row = mysqli_fetch_assoc($result1)) {
                    echo "<option value='" . $row['cid'] . "'>" . $row['cname'] . "</option>";
                }
            } else {
                echo "<option value='' disabled>No categories available</option>";
            }
            ?>
        </select>

        <select class="mt-3 border border-2 border-dark p-1" name="product-unit" placeholder="Select Unit" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:200px">
            <option value="" disabled selected>Select Unit</option>
            <option value="3">Piece</option>
            <option value="1">Liter (L)</option>
            <option value="2">Kilogram (kg)</option>
        </select>

        <select class="mt-3 border border-2 border-dark p-1" name="product-varient-type" placeholder="Select Selling type" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:200px">
            <option value="" disabled selected>Stock Type</option>
            <option value="single">Single stock</option>
            <option value="multi">Multiple stock</option>
        </select>

        <button type="submit" class="btn btn-success">Add Product</button>
    </form>
    <?php
    if (isset($update_id)) {
        $sql = "SELECT * FROM products WHERE product_id = $update_id";
        $result2 = mysqli_query($conn, $sql);

        if ($result2 && mysqli_num_rows($result2) > 0) {
            $product = mysqli_fetch_assoc($result2);

            $sql = "SELECT cid, cname FROM category";
            $result3 = mysqli_query($conn, $sql);
    ?>
            <form class="update-fields mt-3 col-md-6" id="add-product" action=".././process/inventory/update-product.php" method="POST">
                <input type="hidden" class="mt-3 mb-1 border border-2 border-dark p-1" name="update-product-id" value="<?php echo $product['product_id'] ?>" placeholder="Product Name" required style="outline: none;">
                <input type="text" class="mt-3 mb-1 border border-2 border-dark p-1" name="update-product-name" value="<?php echo $product['product_name'] ?>" placeholder="Product Name" required style="width:200px;outline: none;">

                <select class="mt-3 border border-2 border-dark p-1" name="update-product-category" value="<?php echo $product['category_id'] ?>" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:180px">
                    <option value="" disabled>Select Category</option>
                    <?php
                    if (mysqli_num_rows($result3) > 0) {
                        while ($row = mysqli_fetch_assoc($result3)) {
                            $selected = ($row['cid'] == $product['category_id']) ? 'selected' : '';
                            echo "<option value='" . $row['cid'] . "' " . $selected . ">" . $row['cname'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No categories available</option>";
                    }
                    ?>
                </select>

                <select class="mt-3 border border-2 border-dark p-1" name="update-product-unit" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:200px">
                    <option value="" disabled>Select Unit</option>
                    <option value="3" <?php echo ($product['unit_id'] == '3') ? 'selected' : ''; ?>>Piece</option>
                    <option value="1" <?php echo ($product['unit_id'] == '1') ? 'selected' : ''; ?>>Liter (L)</option>
                    <option value="2" <?php echo ($product['unit_id'] == '2') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                </select>

                <select class="mt-3 border border-2 border-dark p-1" name="update-product-variant-type" required style="width:200px;outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-color: white; width:200px">
                    <option value="" disabled>Stock Type</option>
                    <option value="single" <?php echo ($product['stock_type'] == 'single') ? 'selected' : ''; ?>>Single stock</option>
                    <option value="multi" <?php echo ($product['stock_type'] == 'multi') ? 'selected' : ''; ?>>Multi stock</option>
                </select>
                <button type="submit" class="update-data btn btn-primary">Update Product</button>
            </form>
    <?php } else {
            echo 'No Product Found';
        }
    } ?>

</div>

<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <form class="d-flex align-items-center w-50 p-1 search-bar-container" method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="option" value="inventory">
        <label for="inventorySearch">Search Product</label>
        <input class="search-input border border-2 border-dark p-1 w-50 mx-1" type="text" id="inventorySearch" name="search" placeholder="Product name / Product id" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn btn-dark">Search</button>
    </form>
</div>

<div class="table-responsive" style="height: 60vh; overflow-y:scroll;">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th scope="col">Product ID</th>
                <th scope="col">Product Name</th>
                <th scope="col">Category</th>
                <th scope="col">Quantity</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">

            <?php
            if (isset($_GET['search'])) {
                $search = mysqli_escape_string($conn, $_GET['search']);
                $sql = "SELECT * FROM products WHERE product_id LIKE '%$search%' OR product_name LIKE '%$search%' OR category_id LIKE '%$search%' OR total_quantity LIKE '%$search%'";
                $result4 = mysqli_query($conn, $sql);

                if (!$result4) {
                    echo "Error: " . mysqli_error($conn);
                } elseif (mysqli_num_rows($result4) > 0) {
                    while ($row = mysqli_fetch_assoc($result4)) {
                        echo "<tr>
            <td>" . $row['product_id'] . "</td>  
            <td>" . $row['product_name'] . "</td>";

                        $category_id = $row['category_id'];
                        $sql6 = "SELECT cname FROM category WHERE cid = $category_id";
                        $result6 = mysqli_query($conn, $sql6);
                        if ($row1 = mysqli_fetch_assoc($result6)) {
                            echo "<td>" . $row1['cname'] . "</td>";
                        } else {
                            echo "<td>Unknown Category</td>";
                        }

                        if ($row['stock_type'] == 'single') {
                            echo "<td>" . ($row['total_quantity'] !== null ? $row['total_quantity'] : '0') . "</td>";

                        } else if ($row['stock_type'] == 'multi') {
                            $productId = $row['product_id'];
                            
                            $sqlVariant = "SELECT IFNULL(SUM(variant_quantity), 0) as total_variant_quantity 
                                           FROM product_variants 
                                           WHERE product_id = $productId";
                                           
                            $resultVariant = mysqli_query($conn, $sqlVariant);
                        
                            if ($resultVariant) {
                                $variantRow = mysqli_fetch_assoc($resultVariant);
                                echo "<td>" . (!is_null($variantRow['total_variant_quantity']) ? $variantRow['total_variant_quantity'] : '0') . "</td>";
                            } else {
                                echo "<td>0</td>";
                            }
                        }

                        echo "<td>
                    <a href='./admin-panel.php?option=stock-manage&&pid=" . $row['product_id'] . "'><button class='btn btn-success btn-sm me-2'>Stock Manage</button></a>
                    <a href='./admin-panel.php?option=inventory&&uid=" . $row['product_id'] . "'><button class='update btn btn-primary btn-sm me-2'>Update</button></a>
                    <a href='.././process/inventory/delete-inventory.php?pid=" . $row['product_id'] . "'><button class='delete btn btn-danger btn-sm' >Delete</button></a>
                  </td>
            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found</td></tr>";
                }
            } else {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
            <td>" . $row['product_id'] . "</td>
            <td>" . $row['product_name'] . "</td>";

                        $category_id = $row['category_id'];
                        $sql6 = "SELECT cname FROM category WHERE cid = $category_id";
                        $result6 = mysqli_query($conn, $sql6);
                        if ($row1 = mysqli_fetch_assoc($result6)) {
                            echo "<td>" . $row1['cname'] . "</td>";
                        } else {
                            echo "<td>Unknown Category</td>";
                        }

                        if ($row['stock_type'] == 'single') {
                            echo "<td>" . ($row['total_quantity'] !== null ? $row['total_quantity'] : '0') . "</td>";

                        } else if ($row['stock_type'] == 'multi') {
                            $productId = $row['product_id'];
                            
                            $sqlVariant = "SELECT IFNULL(SUM(variant_quantity), 0) as total_variant_quantity 
                                           FROM product_variants 
                                           WHERE product_id = $productId";
                                           
                            $resultVariant = mysqli_query($conn, $sqlVariant);
                        
                            if ($resultVariant) {
                                $variantRow = mysqli_fetch_assoc($resultVariant);
                                echo "<td>" . (!is_null($variantRow['total_variant_quantity']) ? $variantRow['total_variant_quantity'] : '0') . "</td>";
                            } else {
                                echo "<td>0</td>";
                            }
                        }

                        echo "<td>
                    <a href='./admin-panel.php?option=stock-manage&&pid=" . $row['product_id'] . "'><button class='btn btn-success btn-sm me-2'>Stock Manage</button></a>
                    <a href='./admin-panel.php?option=inventory&&uid=" . $row['product_id'] . "'><button class='update btn btn-primary btn-sm me-2'>Update</button></a>
                    <a href='.././process/inventory/delete-inventory.php?pid=" . $row['product_id'] . "'><button class='delete btn btn-danger btn-sm' >Delete</button></a>
                  </td>
            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products available</td></tr>";
                }
            }
            ?>

        </tbody>
    </table>
</div>