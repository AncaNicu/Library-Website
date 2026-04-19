<?php
include('../includes/connect.php');
session_start();

$numberOfOrders = 0;

if (isset($_SESSION['user_id'])) {
    //obtine user_id pt userul logat
    $userId = $_SESSION['user_id'];

    //pt a obtine toate cosurile finalizate ale userului crt
    $userOrdersQuery = "SELECT * FROM cart WHERE user_id = $userId AND order_status = 'finalized'";
    $userOrdersResult = mysqli_query($conn, $userOrdersQuery);

    $orders = mysqli_fetch_all($userOrdersResult, MYSQLI_ASSOC);

    foreach ($orders as $order) {
        $numberOfOrders += 1;
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-cCE8D2EU7P2BwhaAT0MvBvF46PxpHEFcHTw0C6WYs9bPimU/4AXAzKofyWPowIax" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .orders_list {
            margin: 50px auto; /* centreaza orizontal */
            padding: 20px;
            box-sizing: border-box;
            width: 75%; /* seteaza latimea */
            background-color: #f4f4f4;
            border-radius: 10px;
            overflow-y: auto;
            max-height: 300px;
            border: 1px solid #ddd;
        }

        .order-row {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .order-row-section, .order-row-section-title {
            width: 25%;
        }

        .order-row-section-title {
            font-weight: bold;
        }

        .order-details-btn {
            padding: 10px;
            background-color: #32cd32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .order-details-btn:hover {
            background-color: #8cabff;
        }

        a {
            text-decoration: none;
        }

    </style>
</head>
<body>
    <!-- navbar-ul -->
    <?php include("navbar.php"); ?>

        <!-- divul care contine toate comenzile -->
        <div class="orders_list">
            <?php
                if($numberOfOrders == 0) {
                    echo "<h2>You have no previous orders!</h2>";
                } else {
                    echo "<h2>Your Orders</h2>";
                }

                $contor = 1
            ?>
            
            <div class="order-row">
                <div class="order-row-section-title">
                    <p>No</p>
                </div>
                <div class="order-row-section-title">
                    <p>Date and Time</p>
                </div>
                <div class="order-row-section-title">
                    <p>Order Price</p>
                </div>
            </div>

            <?php foreach ($orders as $order): ?>
                <div class="order-row">
                    <div class="order-row-section">
                        <p><?php echo $contor; ?></p>
                    </div>
                    <div class="order-row-section">
                        <p><?php echo $order['date_time']; ?></p>
                    </div>
                    <div class="order-row-section">
                        <p><?php echo '$' . number_format($order['cart_total_price'], 2); ?></p>
                    </div>
                    <div class="order-row-section">
                        <a href="order_details.php?cart_id=<?php echo $order['cart_id']; ?>" class="order-details-btn">Details</a>
                    </div>
                    <?php $contor++; ?>
                </div>
                
            <?php endforeach; ?>
        </div>

    <!-- footer-ul -->
    <?php include("../includes/footer.php"); ?>

</body>
</html>