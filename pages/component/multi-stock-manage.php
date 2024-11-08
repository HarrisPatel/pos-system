<?php
ob_start();
include '../config.php';
$pid = $_GET['pid'];

if (isset($_GET['vid'])) {
    $vid = $_GET['vid'];

    $sql4 = "SELECT * FROM product_variants WHERE variant_id = $vid";
    $result4 = mysqli_query($conn, $sql4);
    $variant = mysqli_fetch_assoc($result4);
}

$sql = "SELECT * FROM products WHERE product_id = $pid";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if ($product['stock_type'] == 'single') {

    echo '<script type="text/javascript">
        window.location.href = "./admin-panel.php?option=stock-manage&&pid=' . $pid . '";
      </script>';
    exit;
}

if ($product['stock_type'] == 'single') {
    header('Location: ./admin-panel.php?option=stock-manage&&pid=' . $pid);
    exit();
}

$unit_id = $product['unit_id'];

$sql2 = "SELECT unit_name FROM units WHERE unit_id = $unit_id ";
$result2 = mysqli_query($conn, $sql2);
$unit = mysqli_fetch_assoc($result2);

$sql3 = "SELECT * FROM product_variants WHERE product_id = $pid";
$result3 = mysqli_query($conn, $sql3);

?>
<main class="container  mt-0">
    <h2 class="text-center mb-4 pb-3 border-bottom"><?php echo $product['product_name'] ?> Stock</h2>
    <div class="category-form row">
        <div class="mb-3 col-md-5">
            <?php if (!isset($vid)) { ?>
                <div class="add-product-variant">
                    <h4 class="mb-4 pb-3 mt-4 border-bottom">Add New Product Varient</h4>
                    <form action=".././process/inventory/add-variant.php" method="POST" style="width : 80%">
                        <input type="hidden" class="form-control" name="product_id" value="<?php echo  $product['product_id'] ?>">
                        <input type="hidden" class="form-control" name="product_type" value="<?php echo  $product['stock_type'] ?>">
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Varient Name : </b></label>
                            <div class="w-100">
                                <input type="text" class="form-control w-100 mt-2 mb-2" name="variant_name" placeholder="Enter name" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(With All Details For POS search)</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Varient quantity : </b></label>
                            <div class="w-100">
                                <input type="number" step="any" class="form-control w-100 mt-2 mb-2" name="variant_quantity" placeholder="Enter Quantity (Optional)" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(how much quantity you Have)</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Selling quantity : </b></label>
                            <div class="w-100">
                                <input type="number" step="any" class="form-control w-100 mt-2 mb-2" name="variant_sell" placeholder="Enter selling Quantity" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(how much quantity you want to sell)</p>
                            </div>

                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Varient price : </b></label>
                            <div class="w-100">
                                <input type="number" class="form-control w-100 mt-2 mb-2" name="variant_price" placeholder="Enter Price" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(The price of the product)</p>
                            </div>
                        </div>
                        <div class="w-100">
                            <button type="submit" class="remove-btn btn btn-primary w-100 mt-4 float-end">Add Product</button>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
                <div class="update-product-variant">
                    <h4 class="mb-4 pb-3 mt-4 border-bottom">Update Product Varient</h4>
                    <form action=".././process/inventory/update-variant.php" method="POST" style="width : 80%">

                        <input type="hidden" class="form-control" name="product_id" value="<?php echo  $product['product_id'] ?>">
                        <input type="hidden" class="form-control" name="product_type" value="<?php echo  $product['stock_type'] ?>">
                        <input type="hidden" class="form-control" name="variant_id" value="<?php echo  $variant['variant_id'] ?>">
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Varient Name : </b></label>
                            <div class="w-100">
                                <input type="text" class="form-control w-100 mt-2 mb-2" name="variant_name" value="<?php echo $variant['variant_name'] ?>" placeholder="Enter name" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(With All Details For POS search)</p>
                            </div>
                        </div>
                       
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Selling quantity : </b></label>
                            <div class="w-100">
                                <input type="number" step="any" class="form-control w-100 mt-2 mb-2" name="variant_sell" value="<?php echo $variant['sell_quantity'] ?>" placeholder="Enter selling Quantity" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(how much quantity you want to sell)</p>
                            </div>

                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <label for="" style="width:230px"><b>Varient price : </b></label>
                            <div class="w-100">
                                <input type="number" class="form-control w-100 mt-2 mb-2" name="variant_price" value="<?php echo $variant['variant_price'] ?>" placeholder="Enter Price" required style="border: 1px solid gray;">
                                <p style="font-size:12px; font-style:italic;margin:0">(The price of the product)</p>
                            </div>
                        </div>
                        <div class="w-100 d-flex gap-2">
                            <a class="w-50" href="./admin-panel.php?option=multi-stock-manage&&pid=<?php echo $variant['product_id'] ?>"><button type="button" class="cancel-btn btn btn-primary w-100 mt-4 float-end">Cancel</button></a>
                            <button type="submit" class="update-btn btn btn-success w-50 mt-4 float-end">Save Product</button>
                        </div>
                    </form>
                </div>
            <div class="row">
            <h4 class=" pb-3 mt-4 border-bottom">Stock Manage</h4>
                <h5></h5>
                <h5 class="mb-3 mt-1"><b>Total Quantity : </b><?php echo $variant['variant_quantity'] . " " . $unit['unit_name'] ?></h5>
                <form action=".././process/inventory/add-stock.php" method="post">
                    <input type="number" step="any" class="add-stock mb-3 border border-2 border-dark p-1" name="add-stock" placeholder="Add Quantity" required style="outline: none;">
                    <input type="hidden" name="pid" value="<?php echo $pid ?>">
                    <input type="hidden" name="vid" value="<?php echo $variant['variant_id'] ?>">
                    <button type="submit" class="add-btn btn btn-success w-50">Add Stock</button>
                </form>
                <form action=".././process/inventory/remove-stock.php" method="post">
                    <input type="hidden" name="vid" value="<?php echo $variant['variant_id'] ?>">
                    <input type="hidden" name="pid" value="<?php echo $pid ?>">
                    <input type="number" step="any" class="remove-stock mb-3 border border-2 border-dark p-1" name="remove-stock" placeholder="Add Quantity" required style="outline: none;">
                    <button type="submit" class="remove-btn btn btn-danger w-50">Remove Stock</button>
                </form>

            </div>
            <?php } ?>

        </div>
        <div class="mb-3 col-md-7">
            <h4 class="mb-4 pb-3 mt-4 border-bottom">All Variants</h4>

            <div class="table-responsive" style="height: 55vh; overflow-y:scroll">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col" style="width:34%">Variant Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="users-list">
                        <?php
                        if (mysqli_num_rows($result3) > 0) {
                            while ($row = mysqli_fetch_assoc($result3)) {
                                echo "
                    <tr>
                        <td>" . $row['variant_id'] . "</td>
                        <td>" . $row['variant_name'] . "</td>
                        <td>" . $row['variant_quantity'] . "</td>
                        <td>Rs." . $row['variant_price'] . "</td>
                 
                        <td>
                        <a href='./admin-panel.php?option=multi-stock-manage&&pid=" . $row['product_id'] . "&&vid=" . $row['variant_id'] . "'><button class='update btn btn-sm btn-primary '>Update</button></a>
                        <a href='.././process/inventory/delete-variant.php?vid=" . $row['variant_id'] . "&&pid=" . $row['product_id'] . "'><button class='delete btn btn-sm btn-danger'>Delete</button></a>
                        </td>
                   </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="5">No product variant found.</td></tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</main>