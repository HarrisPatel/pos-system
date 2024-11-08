<?php include '.././config.php';

$dailySalesQuery = "SELECT SUM(sub_price) AS total_daily_sales FROM sales WHERE DATE(sale_date) = CURDATE()";
$dailySalesResult = mysqli_query($conn, $dailySalesQuery);
if ($dailySalesResult) {
    $dailySales = mysqli_fetch_assoc($dailySalesResult)['total_daily_sales'] ?? 0;
} else {
    echo "Error in daily sales query: " . mysqli_error($conn);
    $dailySales = 0;
}

$weeklySalesQuery = "SELECT SUM(sub_price) AS total_weekly_sales FROM sales WHERE YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)";
$weeklySalesResult = mysqli_query($conn, $weeklySalesQuery);
if ($weeklySalesResult) {
    $weeklySales = mysqli_fetch_assoc($weeklySalesResult)['total_weekly_sales'] ?? 0;
} else {
    echo "Error in weekly sales query: " . mysqli_error($conn);
    $weeklySales = 0;
}

$monthlySalesQuery = "SELECT SUM(sub_price) AS total_monthly_sales FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())";
$monthlySalesResult = mysqli_query($conn, $monthlySalesQuery);
if ($monthlySalesResult) {
    $monthlySales = mysqli_fetch_assoc($monthlySalesResult)['total_monthly_sales'] ?? 0;
} else {
    echo "Error in monthly sales query: " . mysqli_error($conn);
    $monthlySales = 0;
}

$annualSalesQuery = "SELECT SUM(sub_price) AS total_annual_sales FROM sales WHERE YEAR(sale_date) = YEAR(CURDATE())";
$annualSalesResult = mysqli_query($conn, $annualSalesQuery);
if ($annualSalesResult) {
    $annualSales = mysqli_fetch_assoc($annualSalesResult)['total_annual_sales'] ?? 0;
} else {
    echo "Error in annual sales query: " . mysqli_error($conn);
    $annualSales = 0;
}
?>
<main class="container mt-4">
    <h2 class=" mb-4 pb-3 border-bottom">Sales Report</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-box">
                <h4>Today's Sales</h4>
                <p>Rs.<?php echo number_format($dailySales) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <h4>Week's Sales</h4>
                <p>Rs.<?php echo number_format($weeklySales) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <h4>Monthly Sales</h4>
                <p>Rs.<?php echo number_format($monthlySales) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <h4>Annual Sales</h4>
                <p>Rs.<?php echo number_format($annualSales) ?></p>
            </div>
        </div>
    </div>

    <div class="total-sales">
        <h5>Today's Sales Report</h5>
        <h2><strong>Total Sales: Rs.<?php echo number_format($dailySales) ?></strong></h2>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Quantity Sold</th>
                    <th scope="col">Price</th>
                    <th scope="col">Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                SELECT s.*, pv.variant_name 
                FROM sales s
                JOIN product_variants pv ON s.variant_id = pv.variant_id";

                $result4 = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result4) > 0) {
                    while ($row = mysqli_fetch_assoc($result4)) {
                        echo "<tr>
                      <td>" . $row['variant_name'] . "</td>
                      <td>" . $row['sold_quantity'] . "</td>
                      <td>Rs." . $row['sale_price'] . "</td>
                      <td>Rs." . $row['sub_price'] . "</td>
                  </tr>";
                    }
                } else {
                    echo '<tr><td colspan="4">No Sales</td></tr>';
                }
                ?>

            </tbody>
        </table>
    </div>
</main>