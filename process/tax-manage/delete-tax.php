<?php
include '../../config.php';
session_start();


$tid = mysqli_real_escape_string($conn, $_GET['tid']);

$check_sql = "DELETE FROM taxes WHERE tax_id = '$tid'";
$result = mysqli_query($conn, $check_sql);

header('Location: ../../pages/admin-panel.php?option=tax');
?>
