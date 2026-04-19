<?php
include('../includes/connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //obtine id-ul pt cart item selectat 
    $cartItemId = isset($_POST['cartItemId']) ? $_POST['cartItemId'] : '';

    //sterge acel item din baza de date
    $deleteCartItemQuery = "DELETE FROM cart_item WHERE cart_item_id = $cartItemId";
    mysqli_query($conn, $deleteCartItemQuery);

    echo "Cart item deleted successfully!";
} else {
    echo "Invalid request!";
}
?>
