<?php
include '.././config.php';

?>
<style>
    body {
        background-color: #f4f6f9;
    }

    .container {
        margin-top: 30px;
    }

    .table thead {
        background-color: #007bff;
        color: #fff;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    .table tbody tr:nth-child(even) {
        background-color: #e9ecef;
    }

    .btn-action {
        padding: 5px 10px;
        margin: 0 5px;
    }
</style>

<div class="container">
    <h2 class="mb-4 text-center">Returned Items - Admin Panel</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 30%;">Product</th>
                    <th scope="col" style="width: 10%;">Quantity Sold</th>
                    <th scope="col" style="width: 10%;">Price</th>
                    <th scope="col" style="width: 10%;">Total Price</th>
                    <th scope="col" style="width: 20%;">Reason</th>
                    <th scope="col" style="width: 30%;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                SELECT r.*, pv.variant_name 
                FROM return_items r
                JOIN product_variants pv ON r.variant_id = pv.variant_id";

                $result4 = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result4) > 0) {
                    while ($row = mysqli_fetch_assoc($result4)) {
                        echo "<tr>
                      <td>" . $row['variant_name'] . "</td>
                      <td>" . $row['quantity'] . "</td>
                      <td>" . $row['price'] . "</td>
                      <td>" . $row['sub_price'] . "</td>
                      <td>" . $row['reason'] . "</td>
                      <td>" . date('d-m-Y', strtotime($row['date'])) . "</td>
                  </tr>";
                    }
                } else {
                    echo '<tr><td colspan="4">No Sales</td></tr>';
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>