<?php
include('../includes/connect.php');

//ia interogarea de la cererea AJAX
$searchQuery = $_POST['query'];

//obtine toate cartile cu titlul potrivit
$query = "SELECT book_title FROM book WHERE book_title LIKE '%{$searchQuery}%' LIMIT 10";
$result = mysqli_query($conn, $query);

//creeaza un array cu rezultatul interogarii
$titles = array();

if(mysqli_num_rows($result) > 0){
    //aduce rezultatele si le adauga la array-ul de titluri
    while ($row = mysqli_fetch_assoc($result)) {
        $titles[] = $row['book_title'];
    }
}
//returneaza rezultatul sub forma de JSON
echo json_encode($titles);

?>
