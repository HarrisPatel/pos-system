<?php
$message = '';
include "./config.php";

session_start();

if (isset($_SESSION['username']) && $_SESSION['user_type'] == 'admin') {
    header("Location: ./pages/admin-panel.php?option=dashboard");
}elseif(isset($_SESSION['username']) && $_SESSION['user_type'] == 'clerk'){
    header("Location: ./pages/admin-panel.php?option=dashboard");
}elseif(isset($_SESSION['username']) && $_SESSION['user_type'] == 'cashier'){
    header("Location: ./pages/pos-system.php");
}


if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '{$username}'";
    $result = mysqli_query($conn, $sql) or die("Query Failed");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (md5($password) === $row['password']) {

            session_start();
            $_SESSION['key'] = bin2hex(random_bytes(16));
            $_SESSION["id"] = $row['id'];
            $_SESSION["username"] = $row['username'];
            $_SESSION["user_type"] = $row['user_type'];
            if($row['user_type'] == 'cashier'){
            header("Location: ./pages/pos-system.php");
            }elseif($row['user_type'] == 'admin'){
            header("Location: ./pages/admin-panel.php?option=dashboard");
            }
            elseif($row['user_type'] == 'clerk'){
            header("Location: ./pages/admin-panel.php?option=inventory");
            }
            exit();
        } else {
            $message = 'Username or password is incorrect.';
        }
    } else {
        $message = 'Username or password is incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="login-user d-flex justify-content-center" style="margin-top: 250px;">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"
            class="login-box" style="width: 370px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
            <div class="w-100 bg-dark text-light text-center p-3">
                <h2>POS SYSTEM</h2>
            </div>
            <div class="input-fields text-center p-3">
                <label class="mt-2 border-solid-dark" for="" style="font-size: 17px;"><b>username</b></label>
                <input type="text" class="mt-2 w-75 p-1" id="username" name="username" placeholder="Username" style="border: 2px solid black; outline: none;">
                <label class="mt-3" for="" style="font-size: 17px;"><b>Password:</b></label>
                <input type="number" class="w-75 p-1" id="password" name="password" placeholder="password" style="border: 2px solid black; outline: none;">
                <p style="color:red; font-size:12px;margin-left:25px;margin-top:5px"><b><?php echo $message; ?></b></p>

                <button type="submit" name="login" class="btn btn-dark w-25 text-center mt-3">Login</button>
            </div>
        </form>
    </div>
</body>

</html>