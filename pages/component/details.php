<?php
include '.././config.php';
$sql = "SELECT * FROM store";
$result = mysqli_query($conn,$sql);
$fetch = mysqli_fetch_assoc($result);
$storeName = $fetch['storeName'];
$storeAddress = $fetch['storeAddress'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Information Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="form-title">Store Information</h2>
        <form action=".././process/update-store.php" method="POST">
            <div class="mb-3">
                <label for="storeName" class="form-label">Store Name</label>
                <input type="text" value="<?php echo $storeName ?>" class="form-control" name="storeName" value="" placeholder="Enter your store name" required>
            </div>
            <div class="mb-3">
                <label for="storeAddress" class="form-label">Store Address</label>
                <textarea class="form-control" name="storeAddress" rows="3" placeholder="Enter your store address" required><?php echo $storeAddress; ?></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100">Submit</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
