<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip POST inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $newCategoryName = $data['new_category_name'];

    //verifica daca categoria exista deja
    $checkCategoryQuery = $conn->prepare("SELECT category_id FROM category WHERE category_name = ?");
    $checkCategoryQuery->bind_param("s", $newCategoryName);
    $checkCategoryQuery->execute();
    $checkCategoryQuery->store_result();

    if ($checkCategoryQuery->num_rows == 0) {
        $checkCategoryQuery->close();

        //nu exista => o insereaza
        $insertCategoryQuery = $conn->prepare("INSERT INTO category (category_name) VALUES (?)");
        $insertCategoryQuery->bind_param("s", $newCategoryName);

        if ($insertCategoryQuery->execute()) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "message" => "Category inserted successfully"));
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(array("status" => "error", "message" => "Error inserting category"));
        }
    } else {
        $checkCategoryQuery->close();
        http_response_code(400); // Bad Request
        echo json_encode(array("status" => "error", "message" => "Category already exists"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only POST method is allowed"));
}
?>
