<?php

include '../config.php';

if(isset($_GET['error'])){
    $error = $_GET['error'];
}

$fetch_sql = "SELECT * FROM taxes ORDER BY tax_id DESC;";
$result = mysqli_query($conn, $fetch_sql);
?>

<main class="container">
    <h2 class=" mb-4 pb-3 border-bottom">Tax Management</h2>
    <div class="row">
        <div class="col-md-4">
            <h3 class=" mb-4 pb-3 border-bottom">Add New Tax</h3>

            <form class="user-form" action=".././process/tax-manage/add-tax.php" method="POST">
                <div class="mb-3">
                    <label for="tax-name" class="form-label">Tax Name</label>
                    <input type="text" class="form-control" name="tax-name" placeholder="Enter Tax Name" required>
                </div>
                <?php if(isset($error) && $error == 'tax-name'){
                    echo '<p class="mt-0 text-danger">tax is already added</p>';
                }
                ?>

                <div class="mb-3">
                    <label for="tax-rate" class="form-label">Tax Rate (%)</label>
                    <input type="number" class="form-control" name="tax-rate" placeholder="Enter Tax Rate %" required >
                </div>

                <?php if(isset($error) && $error == 'tax-rate'){
                    echo '<p class="mt-0 text-danger">Enter Valid Tax</p>';
                }
                ?>

                <button type="submit" id="add-tax" class="btn btn-success w-100">Add Tax</button>
            </form>
        </div>


        <div class="col-md-8">
            <h3 class=" mb-4 pb-3 border-bottom">All Taxes</h3>

            <div class="table-responsive" style="height: 55vh; overflow-y:scroll">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">TAX Name</th>
                            <th scope="col">Tax Rate</th>
                            <th scope="col">Added Date/Time</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="users-list">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "
                                <tr>
                                    <td>" . $row['tax_name'] . "</td>
                                    <td>" . $row['tax_rate'] . "%</td>
                                    <td>" . $row['tax_time'] . "</td>
                                    <td><a href='.././process/tax-manage/delete-tax.php?tid=". $row['tax_id'] . "'><button class='delete btn btn-danger'>Delete</button></a></td>
                                </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="5">No Tax found.</td></tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>