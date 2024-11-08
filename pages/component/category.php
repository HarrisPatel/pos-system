<?php

include '../config.php';

if(isset($_GET['error'])){
    $error = $_GET['error'];
}

$fetch_sql = "SELECT * FROM category ORDER BY cid DESC;";
$result = mysqli_query($conn, $fetch_sql);
?>
<main class="container">
    <h2 class="text-center mb-4 pb-3 border-bottom">Manage Categories</h2>
    <div class="breadcrumb" style="display: none;">
        <p class="mb-0">Category Add Successfully</p>
    </div>
    <div class="delete-breadcrumb" style="display: none;">
        <p class="mb-0">Category Delete Successfully</p>
    </div>
    <div class="category-form">
        <form class="mb-3" action=".././process/category/add-new-category.php" method="POST">
            <input type="text" class="mb-3 border border-2 border-dark p-1" name="name" placeholder="Add New Category" required style="outline: none;">
            <?php 
            if(isset($_GET['error'])){
                echo '<p class="mt-0 text-danger">Category All ready exist</p>';
            }
            ?>
            <button type="submit" id="add-category" class="btn btn-success">Add Category</button>
        </form>
        <form class="d-flex align-items-center w-50 border border-2 border-dark p-1 mb-3"
            action="" method="GET">
            <span style="width: 250px" for="categorySearch"><b>Search Category:</b></span>
            <input class="search-input w-100 border-0 ms-2" name="search" style="outline:none" type="text" id="categorySearch" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <input type="hidden" name="option" value="category">
            <button type="submit" class="btn btn-dark">Search</button>
        </form>


    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Created By</th>
                    <th scope="col">Date/Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="category-list">
                <?php
                if (isset($_GET['option']) && $_GET['option'] == 'category' && isset($_GET['search'])) {
                    $query = mysqli_escape_string($conn, $_GET['search']);
                    $sql = "SELECT * FROM category WHERE cid LIKE '%$query%' OR cname LIKE '%$query%' OR ccreatedby LIKE '%$query%' OR ctime LIKE '%$query%'";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                                <tr>
                                    <td>" . $row['cid'] . "</td>  
                                    <td>" . $row['cname'] . "</td>
                                    <td>" . $row['ccreatedby'] . "</td>
                                    <td>" . $row['ctime'] . "</td>
                                    <td><a href='.././process/category/delete-category.php?cid=" . $row['cid'] . "'><button class='delete btn btn-danger'>Delete</button></a>
                                    </td>
                                </tr>";
                        }
                    } else {
                    }
                } else {

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <tr>
                                <td>" . $row['cid'] . "</td>  
                                <td>" . $row['cname'] . "</td>
                                <td>" . $row['ccreatedby'] . "</td>
                                <td>" . $row['ctime'] . "</td>
                                <td><a href='.././process/category/delete-category.php?cid=" . $row['cid'] . "'><button class='delete btn btn-danger'>Delete</button></a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo '<tr><td colspan="5">No category found.</td></tr>';
                    }
                }

                ?>
            </tbody>
        </table>
    </div>
</main>