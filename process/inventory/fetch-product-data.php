<?php

include '../../config.php';



$pid = $_GET['id'];

$sql = "SELECT * FROM product WHERE pid = $pid";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo '<p style="margin:0">' . $row['pid'] . '</p>
    <p style="margin:0">' . $row['pname'] . '</p>
    <p style="margin:0">' . $row['pcategory'] . '</p>
    <p style="margin:0">' . $row['pquantity'] . '</p>
    <p style="margin:0">Rs.' . $row['pprice'] . '</p>';
