<?php

session_start();

$_SESSION['total-amount'] = $_SESSION['temp-amount'];

header('Location: ../pages/checkout-page.php')

?>