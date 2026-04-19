<?php
include('../includes/connect.php');
session_start();

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
            
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

                //obtine book_id pt cartea pe care am apasat
                $bookId = $_POST['book_id'];

                //verif daca cartea deja exista in cos
                $checkBookQuery = "SELECT * FROM cart_item WHERE cart_id = $cartId AND book_id = $bookId";
                $checkBookResult = mysqli_query($conn, $checkBookQuery);

                //daca cartea nu e in cos, o adauga
                if (mysqli_num_rows($checkBookResult) == 0) {
                    $addToCartQuery = "INSERT INTO cart_item (cart_id, book_id, item_quantity) VALUES ($cartId, $bookId, 1)";
                    mysqli_query($conn, $addToCartQuery);

                    //seteaza mesajul pe care sa-l afiseze in alert si flagul pt succes
                    $response['success'] = true;
                    $response['message'] = 'Book successfully added to the cart!';

                } else {
                    //seteaza mesajul pe care sa-l afiseze in alert si flagul pt esec
                    $response['success'] = false;
                    $response['message'] = 'This book is already in the cart!';
                }
}
//converteste raspunsul (mesajul si flagul) in format JSON si il trimite scriptului
header('Content-Type: application/json');
echo json_encode($response);
?>
