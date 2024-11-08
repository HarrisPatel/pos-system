<?php
session_start();
include '../config.php';

if (!isset($_SESSION['total-amount'])) {
    header('Location: ./pos-system.php');
    exit();
}

if (!isset($_GET['receipt_no'])) {
    header('Location: ./pos-system.php');
    exit();
}

$receiptNo = $_GET['receipt_no'];

$salesQuery = "SELECT sales.*, product_variants.variant_name 
FROM sales 
INNER JOIN product_variants ON sales.variant_id = product_variants.variant_id 
WHERE receipt_no = '$receiptNo'";
$salesResult = mysqli_query($conn, $salesQuery);

$receiptQuery = "SELECT * FROM receipts WHERE receipt_no = '$receiptNo'";
$receiptResult = mysqli_query($conn, $receiptQuery);

if ($receiptResult && mysqli_num_rows($receiptResult) > 0) {
    $receiptData = mysqli_fetch_assoc($receiptResult);

    $sql = "SELECT * FROM store";
    $result = mysqli_query($conn, $sql);
    $fetch = mysqli_fetch_assoc($result);
    $storeName = $fetch['storeName'];
    $storeAddress = $fetch['storeAddress'];
} else {
    echo "Receipt details not found!";
    exit();
}

unset($_SESSION['total-amount']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bill-page.css">
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1 class="mart-name"><b><?php echo $storeName ?></b></h1>
            <p class="address">Adress: <?php echo $storeAddress ?></p>
            <p class="receipt-number">Receipt No: <?php echo htmlspecialchars($receiptData['receipt_no']); ?></p>
            <p class="cashier">Cashier: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p class="checkout-time">Date & Time: <?php echo date('Y-m-d H:i:s', strtotime($receiptData['checkout_time'])); ?></p>
        </div>

        <div class="receipt-body">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($salesResult && mysqli_num_rows($salesResult) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($salesResult)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['variant_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['sold_quantity']); ?></td>
                                <td><?php echo number_format($row['sale_price'], 2); ?></td>
                                <td><?php echo number_format($row['sub_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No products found for this receipt.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-footer">
            <p><b>Total: </b><?php echo number_format($_SESSION['get-total'], 2); ?></p>
            <div style="border-bottom:1px dashed black;padding-bottom:10px">
                <?php
                $query = "SELECT tax_name, tax_rate FROM taxes";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<h6 ><b>' . $row['tax_name'] . ' Tax :</b> ' . $row['tax_rate'] . '%</h6>';
                    }
                }
                ?>

            </div>
            <p style="padding-top:10px"><b>Total Amount: </b><?php echo number_format($receiptData['total_amount'], 2); ?></p>
            <p><b>Received Amount: </b><?php echo number_format($receiptData['received_amount'], 2); ?></p>
            <p style="border-bottom:1px dashed black; padding-bottom:10px"><b>Change: </b><?php echo number_format($receiptData['change_amount'], 2); ?></p>

            <p style="text-align :center; margin-top:30px">No Return No Exchange Without Recipt</p>
        </div>
        <br><br>
        <div class="button-container">
            <a href="./pos-system.php" class="btn btn-primary return-btn">Return to POS</a>
            <a href="./pos-system.php" class="btn btn-danger print-btn">Print Recipt</a>
        </div>
    </div>
</body>

</html>