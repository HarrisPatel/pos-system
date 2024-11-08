<?php

include '../config.php';

$dailySalesQuery = "SELECT SUM(sub_price) AS total_daily_sales FROM sales WHERE DATE(sale_date) = CURDATE()";
$dailySalesResult = mysqli_query($conn, $dailySalesQuery);
if ($dailySalesResult) {
    $dailySales = mysqli_fetch_assoc($dailySalesResult)['total_daily_sales'] ?? 0;
} else {
    echo "Error in daily sales query: " . mysqli_error($conn);
    $dailySales = 0;
}

$item_count_result = mysqli_query($conn, "SELECT COUNT(product_name) AS item_count FROM products");
$item_count = mysqli_fetch_assoc($item_count_result)['item_count'] ?? 0;

$cat_count_result = mysqli_query($conn, "SELECT COUNT(cid) AS cat_count FROM category");
$cat_count = mysqli_fetch_assoc($cat_count_result)['cat_count'] ?? 0;


?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Admin Dashboard</h1>
</div>
<p>Welcome Admin. Your control center awaits.</p>


<div class="row">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h4>Total Sales (Today)</h4>
                <h2 class="display-5"><b>Rs.<?php echo number_format($dailySales) ?></b></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h4>Inventory Status</h4>
                <p class="display-5 mb-0"><?php echo $item_count ?> Items</p>
                <p class="display-5"><?php echo $cat_count ?> categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h4>Recent Transactions</h4>

                <?php
                $recent_transactions = "SELECT * FROM receipts ORDER BY receipt_no DESC LIMIT 3";
                $transactions_result = mysqli_query($conn, $recent_transactions);

                echo '<ul>';
                while ($transaction = mysqli_fetch_assoc($transactions_result)) {
                    echo '<li>Receipt ' . $transaction['receipt_no'] . ' - Rs.' . $transaction['total_amount'] . '</li>';
                }
                echo '</ul>';

                ?>


            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h4>Top Selling Products</h4>
                <?php
                $top_products_query = "
                SELECT pv.variant_name, SUM(s.sold_quantity) AS total_units_sold
                FROM sales s
                JOIN product_variants pv ON s.variant_id = pv.variant_id
                GROUP BY pv.variant_name
                ORDER BY total_units_sold DESC
                LIMIT 3";

                $top_products_result = mysqli_query($conn, $top_products_query);

                echo '<ul>';
                while ($product = mysqli_fetch_assoc($top_products_result)) {
                    echo '<li>' . htmlspecialchars($product['variant_name']) . ' | Total Units Sold - ' . $product['total_units_sold'] . '</li>';
                }
                echo '</ul>';
                ?>

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h4>Quick Actions</h4>
                <a href="./admin-panel.php?option=inventory" class="btn btn-primary me-2">Manage Inventory</a>
                <a href="./admin-panel.php?option=sale-report" class="btn btn-success me-2">View Sales Report</a>
                <a href="./admin-panel.php?option=user-management" class="btn btn-info">Manage Users</a><br>
                <a href="./admin-panel.php?option=details" class="btn btn-danger mt-2">Store Details</a>
            </div>
        </div>
    </div>
</div>

