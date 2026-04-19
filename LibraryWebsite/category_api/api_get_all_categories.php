<?php
require_once('../includes/db_config.php');
//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip GET inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //obtine toate categoriile din baza de date
    $getAllCategoriesQuery = $conn->prepare("SELECT category_id, category_name FROM category");
    $getAllCategoriesQuery->execute();
    $result = $getAllCategoriesQuery->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $getAllCategoriesQuery->close();

    //verifica daca exista categorii
    if (!empty($categories)) {
        http_response_code(200);
        echo json_encode(array("status" => "success", "categories" => $categories));
    } else {
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "No categories found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only GET method is allowed"));
}
?>
