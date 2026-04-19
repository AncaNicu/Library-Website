<?php
include('../includes/connect.php');
session_start();

$totalPrice = 0;
$dateTime = "";
$numberOfBooks = 0;
if (isset($_GET['cart_id'])) {
    $cartId = $_GET['cart_id'];

    //obtine pretul total si data si ora pentru cosul crt
    $cartDetailsQuery = "SELECT date_time, cart_total_price FROM cart WHERE cart_id = $cartId";
    $cartDetailsResult = mysqli_query($conn, $cartDetailsQuery);

    if ($cartDetailsResult) {
        $cartDetails = mysqli_fetch_assoc($cartDetailsResult);

        $dateTime = $cartDetails['date_time'];

        //obtine cartile din cosul crt
        $getCartItemsQuery = "SELECT book.*, cart_item.item_quantity as cart_quantity FROM cart_item
            JOIN book ON cart_item.book_id = book.book_id
            WHERE cart_id = $cartId";
        $getCartItemsResult = mysqli_query($conn, $getCartItemsQuery);

        if ($getCartItemsResult) {
            $cartItems = mysqli_fetch_all($getCartItemsResult, MYSQLI_ASSOC);

            //pt fiecare carte din cos
            foreach ($cartItems as $item) {
                //obtine detaliile cartii
                $bookTitle = $item['book_title'];
                $bookAuthor = $item['book_author'];
                $quantity = $item['cart_quantity'];
                $bookPrice = $item['book_price'];

                //determina pretul total si nr de carti din cos
                $totalPrice += $quantity * $bookPrice;
                $numberOfBooks += $quantity;
            }
        } else {
            echo "Error fetching cart items: " . mysqli_error($conn);
        }
    } else {
        echo "Error fetching cart details: " . mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .container {
            display: flex;
            padding: 50px;
        }

        .left-div, .right-div {
            padding: 20px;
            box-sizing: border-box;
        }

        .left-div {
            flex: 7.5; 
            background-color: #f4f4f4;
            border-radius: 10px;
            padding: 20px;
            overflow-y: auto;
            max-height: 300px;
        }

        .right-div {
            flex: 2.5; 
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
        }

        .book-row {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .book-cover {
            width: 60px;
            height: auto;
            margin-right: 10px;
            border-radius: 5px;
        }

        .order-summary {
            margin-top: 20px;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .book-row-section, .book-row-section-title {
            width: 25%;
        }

        .book-row-section-title {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- navbar-ul -->
    <?php include("navbar.php"); ?>
    <div class="container">
        <div class="left-div">
            <div class="book-row">
                <div class="book-row-section-title">
                    <p>Book Cover</p>
                </div>
                <div class="book-row-section-title">
                    <p>Title and Author</p>
                </div>
                <div class="book-row-section-title">
                    <p>Unit Price</p>
                </div>
                <div class="book-row-section-title">
                    <p>Quantity</p>
                </div>
                <div class="book-row-section-title">
                    <p>Total Price</p>
                </div>
            </div>
            <?php foreach ($cartItems as $item): ?>
                <div class="book-row">
                    <div class="book-row-section">
                        <img src="../admin_area/book_covers/<?php echo $item['book_cover']; ?>" alt="Book Cover" class="book-cover">
                    </div>
                    <div class="book-row-section">
                        <p><?php echo $item['book_title']; ?></p>
                        <p>By <?php echo $item['book_author']; ?></p>
                    </div>
                    <div class="book-row-section">
                        <p><?php echo '$' . number_format($item['book_price'], 2); ?></p>
                    </div>
                    <div class="book-row-section">
                        <p><?php echo $item['cart_quantity']; ?></p>
                    </div>
                    <div class="book-row-section">
                        <p><?php echo '$' . number_format($item['book_price'] * $item['cart_quantity'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="right-div">
            <div class="order-summary">
                <p>Date and Time: <?php echo $dateTime; ?></p>
                <p>Total Price: <?php echo '$' . number_format($totalPrice, 2); ?></p>
                <p>Number of Books: <?php echo $numberOfBooks; ?></p>
            </div>
        </div>
    </div>
    <!-- footer-ul -->
    <?php include("../includes/footer.php"); ?>
</body>
</html>