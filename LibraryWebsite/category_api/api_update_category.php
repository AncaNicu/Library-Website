<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip PUT inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $categoryName = $data['category_name'];
    $newCategoryName = $data['new_category_name'];

    //verifica daca categoria exista
    $checkCategoryQuery = $conn->prepare("SELECT category_id FROM category WHERE category_name = ?");
    $checkCategoryQuery->bind_param("s", $categoryName);
    $checkCategoryQuery->execute();
    $checkCategoryQuery->store_result();

    if ($checkCategoryQuery->num_rows > 0) {
        $checkCategoryQuery->bind_result($categoryId);
        $checkCategoryQuery->fetch();
        $checkCategoryQuery->close();

        //exista => verifica daca noua categorie exista deja
        $checkNewCategoryQuery = $conn->prepare("SELECT category_id FROM category WHERE category_name = ?");
        $checkNewCategoryQuery->bind_param("s", $newCategoryName);
        $checkNewCategoryQuery->execute();
        $checkNewCategoryQuery->store_result();

        if ($checkNewCategoryQuery->num_rows == 0) {
            $checkNewCategoryQuery->close();

            //nu exista => actualizeaza categoria
            $updateCategoryQuery = $conn->prepare("UPDATE category SET category_name = ? WHERE category_id = ?");
            $updateCategoryQuery->bind_param("si", $newCategoryName, $categoryId);

            if ($updateCategoryQuery->execute()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Category updated successfully"));
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(array("status" => "error", "message" => "Error updating category"));
            }
        } else {
            $checkNewCategoryQuery->close();
            http_response_code(400); // Bad Request
            echo json_encode(array("status" => "error", "message" => "New category already exists"));
        }
    } else {
        $checkCategoryQuery->close();
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "Category not found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only PUT method is allowed"));
}
?>
