<?php
session_start();
if (!isset($_SESSION['total-amount'])) {
    header('Location: ./pos-system.php');
    exit(); 
}



$totalAmount = isset($_SESSION['total-amount']) ? $_SESSION['total-amount'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Form</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"],
        input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[readonly] {
            background-color: #f9f9f9;
            cursor: not-allowed;
        }

        @media (max-width: 600px) {
            form {
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <form class="mt-5" action="../process/payment-completed.php" method="post">
        <h2 class="py-4 bg-dark text-light">Checkout</h2>
        <div class="p-4 pt-2 ">
            <p class="mt-0  mb-0 text-sm" style="font-size: 13px; color:gray">Tax Included</p>
            <label for="totalAmount" class=" mb-0">Total Amount (Rs):</label>
            <input type="text" id="totalAmount" name="totalAmount" value="<?php echo $totalAmount; ?>" readonly style="cursor: not-allowed;"><br>

            <label for="receivedAmount">Received Amount (Rs):</label>
            <input type="number" id="receivedAmount" name="receivedAmount" placeholder="Enter received amount"><br>

            <label for="changeAmount">Change Amount (Rs):</label>
            <input type="text" id="changeAmount" name="changeAmount" readonly style="cursor: not-allowed;"><br>

            <button type="submit" class="btn btn-primary w-100" value="Submit" id="submitBtn" disabled>Payment Completed</button>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $('#receivedAmount').on('input', function() {
                var totalAmount = parseFloat($('#totalAmount').val());
                var receivedAmount = parseFloat($(this).val());

                if (!isNaN(receivedAmount) && receivedAmount >= 0) {
                    var changeAmount = receivedAmount - totalAmount;
                    $('#changeAmount').val(changeAmount >= 0 ? changeAmount.toFixed(2) : '0.00');
                    
                    if (receivedAmount >= totalAmount) {
                        $('#submitBtn').prop('disabled', false); 
                    } else {
                        $('#submitBtn').prop('disabled', true); 
                    }
                } else {
                    $('#changeAmount').val('');
                    $('#submitBtn').prop('disabled', true);
                }
            });
        });
    </script>

</body>

</html>
