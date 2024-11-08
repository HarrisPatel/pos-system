<?php
$pageName = strtoupper(basename($_SERVER['PHP_SELF'], ".php"));
?>
<div class="container1 <?php echo $_SESSION['user_type'] !== 'cashier'? 'position-fixed':'' ?> w-100 index-1">
    <header class="d-flex flex-wrap align-items-center justify-content-between py-1 px-3 bg-dark text-light">
        <div class="d-flex col-md-4 mb-2 mb-md-0 align-items-center">
            <span style="font-size: 35px;"><b><?php echo ucfirst($_SESSION['user_type'] . '-Panel'); ?></b></span>
        </div>

        <nav class="navbar navbar-expand-md navbar-light col-md-4">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">

            </div>
        </nav>
        <div class="col-md-4 text-end">
            <?php if($pageName == 'POS-SYSTEM'){ ?>
            <a href="./returns.php">
                <button type="button" class="btn btn-light me-2">Return</button>
            </a>
            <a href="../process/logout.php">
                <button type="button" class="btn btn-outline-light me-2">Logout</button>
            </a>
            <?php }elseif($pageName == 'RETURNS'){ ?>
            <a href="./pos-system.php">
                <button type="button" class="btn btn-light me-2">POS</button>
            </a>
            <a href="../process/logout.php">
                <button type="button" class="btn btn-outline-light me-2">Logout</button>
            </a>
            <?php }?>
           
        </div>
    </header>
</div>