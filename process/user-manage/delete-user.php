<?php
include '../../config.php';
session_start();


$uid = mysqli_real_escape_string($conn, $_GET['uid']);

$check_sql = "DELETE FROM user WHERE id = '$uid'";
$result = mysqli_query($conn, $check_sql);

header('Location: ../../pages/admin-panel.php?option=user-management');
?>
