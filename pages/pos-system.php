<?php
include '../config.php';
session_start();

if (!isset($_SESSION['username']) && $_SESSION['user_type'] !== 'cashier') {
    header('Location: ../index.php');
}
if (isset($_SESSION['total-amount'])) {
    unset($_SESSION['total-amount']);
}
if (isset($_SESSION['temp-amount'])) {
    unset($_SESSION['temp-amount']);
}






?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS SYSTEM</title>
    <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css" class="stylesheet">
</head>

<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>
    body {
        font-family: "Poppins", sans-serif;
    }
</style>

<body>

    <?php include './header.php'; ?>

    <div class="container-fluid">
        <div class="row m-2" style="height: 85.7vh;border: 3px solid black;">
            <div class="col-12 col-md-3 py-o px-0 ">
                <div class="items bg-body-tertiary  p-3" style="overflow-y: scroll;">
                    <h4><b>Items</b></h4>
                    <div class="item-list">
                        <h5 class="text-center mt-5">Wellcome To POS</h5>
                    </div>
                </div>
                <div class="search d-flex justify-content-center bg-body-tertiary p-3">
                    <div>
                        <div class="mt-3">
                            <span>Search Products :</span><input type="text" class="search-input my-2"><br>
                            <?php if (isset($_GET['error']) && $_GET['error'] == 'outofstock') { ?>
                                <p class="text-danger"><b>You have out-of-stock items in your cart</b></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-7 py-o px-0" style=" height: 85vh;">
                <div class="checkout bg-body-tertiary" style="overflow-y: scroll; height: 85vh;">
                    <table class="table table-bordered">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th style="width: 5%;">NO</th>
                                <th style="width: 40%;">Name</th>
                                <th style="width: 5%;">Quantity</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 15%;">Total-Price</th>
                                <th style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-black cart-body">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class=" bg-body-tertiary col-12 col-md-2 " style=" border-left: 2px solid black;position:relative">
                <div class="amount">
                    <h4 class="mt-3"><b>Total</b></h4>
                    <h5 style="border-bottom : 1px solid lightgray" class="py-3"><b>Total : </b><span class="get-total"></span></h5>
                    <div class="tax">
                        <h5 class="pb-1"><b>Taxes</h5>

                        <?php
                        $query = "SELECT tax_name, tax_rate FROM taxes";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<h6><b>' . $row['tax_name'] . ' Tax :</b> ' . $row['tax_rate'] . '%</h6>';
                            }
                        } else {
                            echo '<h6>No taxes found.</h6>';
                        }
                        ?>
                    </div>
                </div>
                <div class="total" style="position: absolute;bottom:20px;width:92%">
                    <div class="totalAmount w-100">
                    </div>
                    <div>
                        <a href="../process/checkout.php"><button class="btn btn-success checkout-btn btn-lg float-end mt-4 w-100"><b>Checkout</b></button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        var sessionKey = '<?php echo isset($_SESSION['key']) ? $_SESSION['key'] : ''; ?>';
        // console.log(sessionKey)

        function loadcart() {
            // console.log(sessionKey)

            $.ajax({
                url: '../process/loadcart.php',
                method: 'POST',
                // data: {
                //     key: sessionKey
                // },
                success: function(response) {
                    if (typeof response === 'string') {
                        var data = JSON.parse(response);
                    } else {
                        var data = response;
                    }

                    if (data.status === 'success') {
                        $('.cart-body').html(data.cartHtml);
                        $('.get-total').html(data.getTotal);
                        $('.totalAmount').html(data.totalAmount);
                        if (data.getTotal == 0) {
                            $('.checkout-btn').prop('disabled', true);
                        }else{
                            $('.checkout-btn').prop('disabled', false);
                        }
                        // sessionKey = data.new_key;
                    } else {
                        console.error('Error: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching product data: ' + error);
                }
            });
        }

        loadcart();

        $('.search-input').on('keyup', function() {
            var search = $('.search-input').val().trim();
            $.ajax({
                url: '../process/search-item.php',
                method: 'GET',
                data: {
                    query: search,
                    key: sessionKey
                },
                success: function(response) {
                    if (typeof response === 'string') {
                        var data = JSON.parse(response);
                    } else {
                        var data = response;
                    }

                    if (data.status === 'success') {
                        $('.item-list').html(data.itemsHtml);
                        sessionKey = data.new_key;

                    } else {
                        console.error('Error: ' + data.message);
                        sessionKey = data.new_key;

                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching product data: ' + error);
                    console.log(xhr.responseText);
                }
            });
        });
        $(document).on('click', '.add-btn', function() {
            console.log('check = ' + sessionKey);
            var productId = $(this).data('pid');
            var variantId = $(this).data('vid');
            var productName = $(this).data('name');
            var productPrice = $(this).data('price');
            var qty = $(this).closest('.item-box').find('.qty-input').val().trim();

            if (qty > 0) {

                $.ajax({
                    url: '../process/add-cart.php',
                    method: 'POST',
                    data: {
                        pid: productId,
                        vid: variantId,
                        name: productName,
                        price: productPrice,
                        qty: qty,
                        key: sessionKey
                    },
                    success: function(response) {
                        if (typeof response === 'string') {
                            var data = JSON.parse(response);
                        } else {
                            var data = response;
                        }

                        if (data.status === 'success') {
                            console.log('success = ' + data.new_key);

                            sessionKey = data.new_key;
                            loadcart()
                        } else {
                            sessionKey = data.new_key;

                            console.log('not = ' + data.new_key);
                            console.error('Error: ' + data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }


        });

        $(document).on('click', '.delete-cart', function() {
            var cartId = $(this).data('cart-id');

            $.ajax({
                url: '../process/delete-cart.php',
                method: 'POST',
                data: {
                    cart_id: cartId,
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
                        loadcart()
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




        function checkQtyInputs() {
            let allValid = true;
            $('.qty-update-input').each(function() {
                if ($(this).val().trim() === "" || $(this).val() <= 0) {
                    allValid = false;
                }
            });
            $('.checkout-btn').prop('disabled', !allValid);
        }

        loadcart();

        $(document).on('input', '.qty-update-input', function() {
            let cid = $(this).data('cart-id');
            let vid = $(this).data('vid');
            let quantity = $(this).val();

            if (quantity > 0) {
                $.ajax({
                    url: '../process/update-cart.php',
                    type: 'GET',
                    data: {
                        cid: cid,
                        vid: vid,
                        qty: quantity,
                        key: sessionKey
                    },
                    success: function(response) {
                        var data = (typeof response === 'string') ? JSON.parse(response) : response;
                        if (data.status === 'success') {
                            sessionKey = data.new_key;
                            loadcart();
                        } else {
                            sessionKey = data.new_key;
                            loadcart();

                        }
                    },
                    error: function() {
                        alert('An error occurred while updating the cart.');
                    }
                });
            }

            checkQtyInputs();
        });


    });
</script>