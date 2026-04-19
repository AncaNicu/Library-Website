<?php
//pt a actualiza cantitatea unui cart_item in baza de date
include('../includes/connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartItemId = isset($_POST['cartItemId']) ? $_POST['cartItemId'] : '';
    $newQuantity = isset($_POST['newQuantity']) ? $_POST['newQuantity'] : '';

    //actualizeaza cantitatea
    $updateQuantityQuery = "UPDATE cart_item SET item_quantity = $newQuantity WHERE cart_item_id = $cartItemId";
    mysqli_query($conn, $updateQuantityQuery);

    echo "Quantity updated successfully!";
} else {
    echo "Invalid request!";
}
?>
