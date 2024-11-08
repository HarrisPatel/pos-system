<?php

include '../config.php';

if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

$fetch_sql = "SELECT * FROM user ORDER BY id DESC;";
$result = mysqli_query($conn, $fetch_sql);
?>

<main class="container">
    <h2 class=" mb-4 pb-3 border-bottom">User Management</h2>
    <div class="breadcrumb" style="display: none;">
        <p class="mb-0">User Add Successfully</p>
    </div>
    <div class="delete-breadcrumb" style="display: none;">
        <p class="mb-0">User Delete Successfully</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h3 class=" mb-4 pb-3 border-bottom">Add New User</h3>

            <form class="user-form" action=".././process/user-manage/add-user.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter username" required>

                </div>
                <?php if (isset($error) && $error == 'username') {
                    echo '<p class="mt-0 text-danger">username already taken</p>';
                }
                ?>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label for="repeatPassword" class="form-label">Repeat Password</label>
                    <input type="password" class="form-control" name="password2" placeholder="Repeat password" required>
                </div>
                <?php if (isset($error) && $error == 'password') {
                    echo '<p class="mt-0 text-danger">Password Not Matched</p>';
                }
                ?>
                <div class="mb-3">
                    <label for="accountType" class="form-label">Account Type</label>
                    <select class="form-select" name="user_type" required>
                        <option value="" disabled selected>Select account type</option>
                        <option value="admin">Admin</option>
                        <option value="clerk">Clerk</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>

                <button type="submit" id="add-user" class="btn btn-success w-100">Add User</button>
            </form>
        </div>


        <div class="col-md-8">
            <h3 class=" mb-4 pb-3 border-bottom">All Users</h3>

            <div class="table-responsive" style="height: 55vh; overflow-y:scroll">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Account Type</th>
                            <th scope="col">Date/Time Created</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="users-list">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "
                                <tr>
                                    <td>" . $row['id'] . "</td>
                                    <td>" . $row['username'] . "</td>
                                    <td>" . $row['user_type'] . "</td>
                                    <td>" . $row['created_at'] . "</td>
                                ";

                                if ($_SESSION['username'] !== $row['username']) {
                                    echo "
                                    <td><a href='.././process/user-manage/delete-user.php?uid=" . $row['id'] . "'><button class='delete btn btn-danger'>Delete</button></a></td>
                                ";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="5">No user found.</td></tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>