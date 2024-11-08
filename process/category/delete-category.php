<?php

include '../../config.php';

$cid = $_GET['cid'];

$sql = "DELETE FROM category WHERE cid = $cid";
$result = mysqli_query($conn,$sql);

$sql = "SELECT cname From category WHERE cid = $cid";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);
$cname = $row['cname'];

$sql2 = "DELETE FROM product WHERE category = $cname";
$result = mysqli_query($conn,$sql);

header('Location: ../../pages/admin-panel.php?option=category');



?>