<?php
include '../config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'cashier') {
    header('Location: ../index.php');
    exit();
}

$sql = 'DELETE FROM return_cart';
$result = mysqli_query($conn, $sql);

$data = false;
$sales_data = [];
$search = '';

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $receipt_sql1 = "SELECT s.*, pv.variant_name,pv.product_id,pv.variant_id  
                     FROM sales s
                     JOIN product_variants pv ON s.variant_id = pv.variant_id 
                     WHERE receipt_no = '$search'";
    $search_sales = mysqli_query($conn, $receipt_sql1);

    if ($search_sales && mysqli_num_rows($search_sales) > 0) {
        $data = true;
        $receipt_sql2 = "SELECT * FROM receipts WHERE receipt_no = '$search'";
        $search_receipt = mysqli_query($conn, $receipt_sql2);
        if ($search_receipt) {
            $receipt_data = mysqli_fetch_assoc($search_receipt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS SYSTEM</title>
    <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>
<style>
    body {
        font-family: "Poppins", sans-serif;
    }
</style>

<body>
    <?php include './header.php'; ?>
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-md-7 checkout">
                <div class="return-search">
                    <form action="" method="GET">
                        <label class="mb-1" style="font-size: 17px;"><b>Enter Receipt No:</b></label>
                        <input type="text" class="text" name="search" style="width: 50%;" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-dark">Search</button>
                    </form>

                </div>
                <div class="mt-3" style="overflow-y: scroll; height: 55vh; border-right:2px solid black">
                    <table class="table table-bordered">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th style="width: 5%;">NO</th>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 10%;">Quantity</th>
                                <th style="width: 10%;">Price</th>
                                <th class="border-left border-dark" style="width: 10%;">Total</th>
                                <th style="width: 10%;">Return</th>
                                <th style="width: 10%;">Reason</th>
                                <th style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-black">
                            <?php if ($data): ?>
                                <?php $no = 1; ?>
                                <?php while ($sales_data = mysqli_fetch_assoc($search_sales)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($sales_data['variant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($sales_data['sold_quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($sales_data['sale_price']); ?></td>
                                        <td class="border-left border-dark"><?php echo htmlspecialchars($sales_data['sub_price']); ?></td>
                                        <?php
                                        $variant_id = $sales_data['variant_id'];
                                        $returnSql = "SELECT SUM(quantity) as quantity FROM return_items WHERE receipt_no = '$search' AND variant_id = $variant_id";
                                        $returnResult = mysqli_query($conn, $returnSql);

                                        $returned = false;

                                        if ($returnResult && mysqli_num_rows($returnResult) > 0) {
                                            $return_quantity_fetch = mysqli_fetch_assoc($returnResult);
                                            $return_quantity = $return_quantity_fetch['quantity'];

                                            if ($return_quantity == $sales_data['sold_quantity']) {
                                                $returned = true;
                                            }
                                        }
                                        ?>
                                        <td><?php echo htmlspecialchars($return_quantity??0); ?></td>
                                        <td>
                                            <select class="select-reason">
                                                <option value="defective">Defective</option>
                                                <option value="expire">Expire</option>
                                                <option value="changed Mind" selected>Changed Mind</option>
                                                <option value="wrong item">Wrong Item</option>
                                                <option value="incorrect quantity">Incorrect Quantity</option>
                                            </select>
                                        </td>

                                        <td class="d-flex gap-3 action return-box">
                                            <input type="number" class="return-input" max="<?php echo $sales_data['sold_quantity']; ?>" min="1" value="1" style="width: 70px;">
                                            <button
                                                data-rno="<?php echo $search; ?>"
                                                data-pid="<?php echo $sales_data['product_id']; ?>"
                                                data-vid="<?php echo $sales_data['variant_id']; ?>"
                                                data-sid="<?php echo $sales_data['sale_id']; ?>"
                                                data-price="<?php echo $sales_data['sale_price']; ?>"
                                                <?php echo $returned ? 'disabled' : ''; ?>
                                                class="btn btn-danger return-btn"
                                                style="font-size:10px">
                                                <?php echo $returned ? 'Returned' : 'Return'; ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data available. Please search with <b>Receipt Number</b></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <h2 class="mt-2"><b> Details </b></h2>
                <?php if(isset($receipt_data)){ ?>
                <div class="cashier-info mt-2 mx-2 d-flex align-itmes-center gap-3">
                    <p class="m-0 px-3" style="background-color: lightblue;border-radius:10px"><b>Cashier name : </b><?php echo $receipt_data['cashier_name']?></p>
                    <p class="m-0 px-3" style="background-color: lightblue;border-radius:10px"><b>Checkout Date : </b><?php echo date('d-m-Y H:i:s', strtotime($receipt_data['checkout_time'])); ?></p>
                </div>
                <?php } ?>
                <div class="d-flex mt-3">
                    <h5 class="p-2 mx-2" style="background-color: lightblue;border-radius:10px">Total Amount : Rs.<?php echo $receipt_data['total_amount'] ?? 0 ?> <span style="font-size: 13px;">(with Tax)</span></h5>
                    <h5 class="p-2 mx-2" style="background-color: lightblue;border-radius:10px">Received Amount : Rs.<?php echo $receipt_data['received_amount'] ?? 0 ?></h5>
                    <h5 class="p-2 mx-2" style="background-color: lightblue;border-radius:10px">Change Amount : Rs.<?php echo $receipt_data['change_amount'] ?? 0 ?></h5>
                </div>
            </div>
            <div class="col-md-5">
                <div>
                    <h1>Return</h1>
                    <div style="overflow-y: scroll; height: 50vh;">
                        <table class="table table-bordered">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th style="width: 30%;">Name</th>
                                    <th style="width: 10%;">Return</th>
                                    <th style="width: 15%;">Price</th>
                                    <th style="width: 15%;">Total</th>
                                    <th style="width: 20%;">Reason</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="return-cart-body bg-white text-black"></tbody>
                        </table>
                    </div>
                </div>
                <h2 class="text-center">Details</h2>
                <h4 class="mt-4" style="color:gray">Total Amount <span style="font-size: 13px;">(without TAX)</span> : Rs.<span class="get-total"></span></h4>
                <h3 class="mt-2">Total Amount <span style="font-size: 13px;">(with TAX)</span> : Rs.<span class="totalAmount"></span></h3>
                <form action="../process/return/return.php" method="POST">
                    <div class="d-flex mx-4" style="float:right">
                        <input type="hidden" name="totalAmount" value="">
                        <input type="hidden" name="receiptNO" value="<?php echo $search ?>">
                        <button class="btn btn-success" type="submit">Returned</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var sessionKey = '<?php echo isset($_SESSION['key']) ? $_SESSION['key'] : ''; ?>';
        var totalAmount = 0

        function loadreturncart() {
            $.ajax({
                url: '../process/return/load-return-cart.php',
                method: 'GET',

                success: function(response) {
                    if (typeof response === 'string') {
                        var data = JSON.parse(response);
                    } else {
                        var data = response;
                    }
                    if (data.status === 'success') {
                        $('.return-cart-body').html(data.return_cartHtml);
                        $('.get-total').html(data.getTotal);
                        $('.totalAmount').html(data.totalAmount);
                        totalAmount = data.totalAmount
                        $('input[name="totalAmount"]').val(totalAmount);
                    } else {
                        console.error('Error: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching product data: ' + error);
                }
            });
        }

        loadreturncart();

        $(document).on('click', '.return-btn', function() {

            var rno = $(this).data('rno');
            var productId = $(this).data('pid');
            var variantId = $(this).data('vid');
            var saleId = $(this).data('sid');
            var Price = $(this).data('price');
            var reason = $('.select-reason').val()
            var qty = $(this).closest('.return-box').find('.return-input').val()
            if (qty > 0) {

                $.ajax({
                    url: '../process/return/add-return-cart.php',
                    method: 'POST',
                    data: {
                        rno: rno,
                        pid: productId,
                        vid: variantId,
                        sid: saleId,
                        price: Price,
                        qty: qty,
                        reason:reason,
                        key: sessionKey
                    },
                    success: function(response) {
                        if (typeof response === 'string') {
                            var data = JSON.parse(response);
                        } else {
                            var data = response;
                        }

                        if (data.status === 'success') {
                            sessionKey = data.new_key;
                            loadreturncart()
                        } else {
                            sessionKey = data.new_key;
                            console.error('Error: ' + data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        sessionKey = data.new_key;
                        console.error('AJAX error:', error);
                    }
                });
            }


        });


        $(document).on('click', '.delete-cart', function() {
            var vid = $(this).data('vid');

            $.ajax({
                url: '../process/return/remove-return-cart.php',
                method: 'POST',
                data: {
                    vid: vid,
                    key: sessionKey
                },
                success: function(response) {
                    if (typeof response === 'string') {
                        var data = JSON.parse(response);
                    } else {
                        var data = response;
                    }

                    if (data.status === 'success') {
                        sessionKey = data.new_key;
                        loadreturncart()
                    } else {
                        sessionKey = data.new_key;

                        console.error('Error: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting product: ' + error);
                }
            });
        });

    });
</script>

</html>