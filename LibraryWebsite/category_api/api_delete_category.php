<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip DELETE inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $categoryName = $data['category_name'];

    //verifica daca categoria exista
    $checkCategoryQuery = $conn->prepare("SELECT category_id FROM category WHERE category_name = ?");
    $checkCategoryQuery->bind_param("s", $categoryName);
    $checkCategoryQuery->execute();
    $checkCategoryQuery->store_result();

    if ($checkCategoryQuery->num_rows > 0) {
        $checkCategoryQuery->bind_result($categoryId);
        $checkCategoryQuery->fetch();
        $checkCategoryQuery->close();

        //exista => sterge cartile asociate ei
        $deleteBooksQuery = $conn->prepare("DELETE FROM book WHERE category_id = ?");
        $deleteBooksQuery->bind_param("i", $categoryId);
        $deleteBooksQueryResult = $deleteBooksQuery->execute();
        $deleteBooksQuery->close();

        //sterge si categoria
        $deleteCategoryQuery = $conn->prepare("DELETE FROM category WHERE category_id = ?");
        $deleteCategoryQuery->bind_param("i", $categoryId);
        $deleteCategoryQueryResult = $deleteCategoryQuery->execute();
        $deleteCategoryQuery->close();

        if ($deleteBooksQueryResult && $deleteCategoryQueryResult) {
            http_response_code(200);
            echo json_encode(array("status" => "success", "message" => "Category and associated books deleted successfully"));
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(array("status" => "error", "message" => "Error deleting category and associated books"));
        }
    } else {
        $checkCategoryQuery->close();
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "Category not found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only DELETE method is allowed"));
}
?>
