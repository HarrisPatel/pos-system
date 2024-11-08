<?php

include '../../config.php';
session_start();

$username = mysqli_real_escape_string($conn, trim($_POST['username']));
$password = md5(trim($_POST['password']));
$password2 = md5($_POST['password2']);
$user_type = $_POST['user_type'];

if (!empty($username) && !empty($password)) {

    $check_sql = "SELECT * FROM user WHERE username = '{$username}'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        header('Location: ../../pages/admin-panel.php?option=user-management&error=username');
        exit();
    } else {
        if ($password == $password2) {
            $sql = "INSERT INTO user (username, password, user_type, created_at) VALUES ('{$username}', '{$password}', '{$user_type}', NOW())";
            if (mysqli_query($conn, $sql)) {
                header('Location: ../../pages/admin-panel.php?option=user-management');
            }
        } else {
            header('Location: ../../pages/admin-panel.php?option=user-management&error=password');
        }
    }
} else {
    header('Location: ../../pages/admin-panel.php?option=user-management');
}
