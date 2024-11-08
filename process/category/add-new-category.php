<?php

include '../../config.php';
session_start();

$name = mysqli_real_escape_string($conn, $_POST['name']);
$created_by = $_SESSION['user_type'];

$check_sql = "SELECT * FROM category WHERE cname = '{$name}'";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) > 0) {
    header('Location: ../../pages/admin-panel.php?option=category&error');
    exit();
} else {
    $sql = "INSERT INTO category (cname, ccreatedby, ctime) VALUES ('{$name}', '{$created_by}', NOW())";
    if (mysqli_query($conn, $sql)) {
        header('Location: ../../pages/admin-panel.php?option=category');
    } else {
        echo 'query not worked';
    }
}

