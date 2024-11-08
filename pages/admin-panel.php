<?php
$selected = $_GET['option'];
session_start();

if (!isset($_SESSION['username']) && $_SESSION['user_type'] !== 'cashier') {
    header('Location: ../index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS SYSTEM</title>
    <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css" class="css">
    <link rel="stylesheet" href="../css/category.css" class="css">
    <link rel="stylesheet" href="../css/inventory.css" class="css">
    <link rel="stylesheet" href="../css/sale-report.css " class="css">
    <link rel="stylesheet" href="../css/user-management.css " class="css">
    <link rel="stylesheet" href="../css/stock-manage.css " class="css">
    <link rel="stylesheet" href="../css/logout.css " class="css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins", sans-serif;
        }

        .sidebar {
            background-color: #1a1a1a;
            min-height: 93.6vh;
        }

        .sidebar .nav-link {
            color: #fff;
            margin-bottom: 15px;
            font-size: 18px;
            border: 1px solid #495057;
            text-align: center;
            padding: 15px 15px;
            border-radius: 5px;
            transition: 0.2s ;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            color: #fff;
        }

        .sidebar .active {
            color: #fff;
            margin-bottom: 15px;
            font-size: 18px;
            background-color: #495057;
            border: 1px solid #495057;
            text-align: center;
            padding: 25px 15px;
            border-radius: 5px;
            transition: 0.2s;
        }

        .content {
            padding: 30px;
        }
    </style>
</head>

<body>

    <?php include './header.php'; ?>


    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar position-fixed" style="margin-top: 61px;">
                <div class="pt-3">
                    <ul class="nav flex-column">
                        <?php if ($_SESSION['user_type'] == 'admin') { ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $selected == 'dashboard' || $selected == 'details' ? 'active' : ''; ?>" href="./admin-panel.php?option=dashboard">
                                    Dashboard
                                </a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link  <?php echo $selected == 'inventory' || $selected == 'stock-manage' || $selected == 'multi-stock-manage' ? 'active' : ''; ?>" href="./admin-panel.php?option=inventory">
                                Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selected == 'sale-report' ? 'active' : ''; ?>" href="./admin-panel.php?option=sale-report">
                                Sales Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selected == 'returns' ? 'active' : ''; ?>" href="./admin-panel.php?option=returns">
                                Returns
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selected == 'category' ? 'active' : ''; ?>" href="./admin-panel.php?option=category">
                                Category
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selected == 'tax' ? 'active' : ''; ?>" href="./admin-panel.php?option=tax">
                               Tax
                            </a>
                        </li>

                        <?php if ($_SESSION['user_type'] == 'admin') { ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $selected == 'user-management' ? 'active' : ''; ?>" href="./admin-panel.php?option=user-management">
                                    User Management
                                </a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selected == 'logout' ? 'active' : ''; ?>" href="./admin-panel.php?option=logout">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 content" style="margin-top: 61px;">
                <?php include './component/' . $selected . '.php' ?>
            </main>
        </div>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>