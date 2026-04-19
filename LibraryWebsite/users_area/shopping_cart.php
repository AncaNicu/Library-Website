<?php
include('../includes/connect.php');
session_start();

//datele pt Order Summary
$numberOfBooks = 0;
$shippingCost = 5.00; //costul de livrare
$totalPrice = 0; //pretul total


if (isset($_SESSION['user_id'])) {
    //obtine user_id pt userul logat
    $userId = $_SESSION['user_id'];

    //verif daca userul are deja un cos activ
    $activeCartQuery = "SELECT * FROM cart WHERE user_id = $userId AND order_status = 'in process'";
    $activeCartResult = mysqli_query($conn, $activeCartQuery);

    if (mysqli_num_rows($activeCartResult) == 0) {
        //nu avem cos activ => se creeaza unul
        $createCartQuery = "INSERT INTO cart (user_id, order_status) VALUES ($userId, 'in process')";
        mysqli_query($conn, $createCartQuery);
    }

    //obtine cart_id (pt cosul existent sau creat anterior)
    $activeCartResult = mysqli_query($conn, $activeCartQuery);
    $cartId = mysqli_fetch_assoc($activeCartResult)['cart_id'];

    //obtine elementele din cart
    $getCartItemsQuery = "SELECT book.*, cart_item.item_quantity as cart_quantity, cart_item.cart_item_id as cart_item_id FROM cart_item
        JOIN book ON cart_item.book_id = book.book_id
        WHERE cart_id = $cartId";
    $getCartItemsQuery = mysqli_query($conn, $getCartItemsQuery);

    // Fetch cart items as an associative array
    $cartItems = mysqli_fetch_all($getCartItemsQuery, MYSQLI_ASSOC);

    foreach ($cartItems as $item) {
        $totalPrice += $item['cart_quantity'] * $item['book_price'];
        $numberOfBooks += $item['cart_quantity'];
    }

    //pt total >= 50 dolari => costul de livrare e 0
    if($totalPrice >= 50.00)
    {
        $shippingCost = 0.00;
    }

    //daca a fost apasat butonul Place Order
    if (isset($_POST['place_order_action']) && $_POST['place_order_action'] === 'place_order') {

        date_default_timezone_set('Europe/Bucharest');

        //obtine data si timpul crt
        $currentDateTime = date('Y-m-d H:i:s');

        //seteaza timpul si data pt cosul crt
        $updateDateTimeQuery = "UPDATE cart SET date_time = '$currentDateTime' WHERE cart_id = $cartId AND order_status = 'in process'";
        mysqli_query($conn, $updateDateTimeQuery);

        $totalPrice += $shippingCost;
        //seteaza pretul total al cosului
        $updateCartPriceQuery = "UPDATE cart SET cart_total_price = '$totalPrice' WHERE cart_id = $cartId AND order_status = 'in process'";
        mysqli_query($conn, $updateCartPriceQuery);

        //pt fiecare carte din cos se actualizeaza calitatea
        //scazand din nr total de exemplare nr de exemplare vandute in cosul crt.
        foreach ($cartItems as $item) {
            $quantitySold = $item['cart_quantity'];
            $bookId = $item['book_id'];
            $updateBookAvailableQuantityQuery = "UPDATE book SET quantity_available = quantity_available - $quantitySold WHERE book_id = $bookId";
            mysqli_query($conn, $updateBookAvailableQuantityQuery);
        }

        //seteaza order_status pt cosul crt ca fiind 'finalized'
        $updateOrderStatusQuery = "UPDATE cart SET order_status = 'finalized' WHERE cart_id = $cartId AND order_status = 'in process'";
        mysqli_query($conn, $updateOrderStatusQuery);

        echo "<script>
            alert('The order was placed!');
            window.location.href = 'books.php';
        </script>";
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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-cCE8D2EU7P2BwhaAT0MvBvF46PxpHEFcHTw0C6WYs9bPimU/4AXAzKofyWPowIax" crossorigin="anonymous">
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
            flex: 7.5; /* 75% of the container */
            background-color: #f4f4f4;
            border-radius: 10px;
            padding: 20px;
            overflow-y: auto;
            max-height: 300px;
        }

        .right-div {
            flex: 2.5; /* 25% of the container */
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

        .quantity-input {
            width: 40px;
            margin-right: 10px;
        }

        .delete-icon {
            color: red;
            cursor: pointer;
        }

        .order-summary {
            margin-top: 20px;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .book-row-section {
            width: 25%;
        }

        #place_order_btn {
            padding: 10px;
            background-color: #32cd32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
        }

        #place_order_btn:hover {
            background-color: #8cabff;
        }

        #place_order_btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 1;
        }

        #place_order_btn:hover:disabled {
            background-color: #ccc;
        }

    </style>
</head>
<body>
    <!-- navbar-ul -->
    <?php include("navbar.php"); ?>

    <div class="container">
        <!-- divul stang contine cosul de cumparaturi -->
        <div class="left-div">
            <?php
                if($numberOfBooks == 0) {
                    echo "<h2>Empty Shopping Cart</h2>";
                } else {
                    echo "<h2>Shopping Cart</h2>";
                }
            ?>
            
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
                        <input type="number" class="quantity-input" value="<?php echo $item['cart_quantity']; ?>" min="1" 
                            max="<?php echo $item['quantity_available']; ?>" data-cart-item-id="<?php echo $item['cart_item_id']; ?>" onkeydown="return false">
                        <i class="fas fa-trash-alt delete-icon" data-cart-item-id="<?php echo $item['cart_item_id']; ?>"></i>
                    </div>
                    <div class="book-row-section">
                        <p><?php echo '$' . number_format($item['book_price'] * $item['cart_quantity'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- divul drept contine rezumatul comenzii -->
        <div class="right-div">
            <div class="order-summary">
                <p>Number of Books: <?php echo $numberOfBooks; ?></p>
                <p>Shipping Cost: <?php echo '$' . number_format($shippingCost, 2); ?></p>
                <p>Total Price: <?php echo '$' . number_format($totalPrice + $shippingCost, 2); ?></p>

                <form method="post" action="">
                    <input type="hidden" name="place_order_action" value="place_order">
                    <button id="place_order_btn" name="place_order_btn" <?php echo count($cartItems) == 0 ? 'disabled' : ''; ?>>
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- footer-ul -->
    <?php include("../includes/footer.php"); ?>

    <script>
    $(document).ready(function() {

        //daca s-a modificat cantitatea unui item din cos
        $('.quantity-input').on('change', function() {
            //obtine noua cantitate si id-ul item-ului
            var newQuantity = $(this).val();
            var cartItemId = $(this).data('cart-item-id');

            //trimite o cerere AJAX pt a actualiza cantitatea in baza de date
            updateQuantity(cartItemId, newQuantity);
        });

        //daca a fost apasat delete pt un item din cos
        $('.delete-icon').on('click', function() {
            //obtine id-ul item-ului din cos
            var cartItemId = $(this).data('cart-item-id');

            //trimite o cerere AJAX pt stergerea acelui item din cos
            deleteCartItem(cartItemId);
        });

        //fct pt  aactualiza cantitatea
        function updateQuantity(cartItemId, newQuantity) {
            $.ajax({
                url: 'update_quantity.php',//fisierul care se ocupa cu actualizarea
                method: 'POST',
                data: { cartItemId: cartItemId, newQuantity: newQuantity },
                success: function(response) {
                    console.log(response);
                    location.reload(); //face refresh la pagina dupa schimbarea cantitatii
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        //fct pt a sterge item-ul din cos
        function deleteCartItem(cartItemId) {
            $.ajax({
                url: 'delete_cart_item.php', //fisierul care se ocupa cu stergerea
                method: 'POST',
                data: { cartItemId: cartItemId },
                success: function(response) {
                    console.log(response);
                    location.reload(); //face refresh la pagina dupa stergerea item-ului
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
    </script>

</body>
</html>