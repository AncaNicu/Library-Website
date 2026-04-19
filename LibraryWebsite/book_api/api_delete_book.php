<?php
require_once('../includes/db_config.php');
include('../admin_area/admin_functions.php');
//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON

header('Content-Type: application/json');

//verifica daca cererea primita este de tip DELETE inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $title = $data['book_title'];
    $author = $data['book_author'];
    $publisherName = $data['selected_publisher'];

    $publisherId = getPublisherIdByName($publisherName, $conn);

    //verifica daca deja exista cartea
    $checkBookQuery = $conn->prepare("SELECT book_id FROM book WHERE book_title = ? AND book_author = ? AND publisher_id = ?");
    $checkBookQuery->bind_param("ssi", $title, $author, $publisherId);
    $checkBookQuery->execute();
    $checkBookQuery->store_result();

    if ($checkBookQuery->num_rows > 0) {
        $checkBookQuery->bind_result($bookId);
        $checkBookQuery->fetch();
        $checkBookQuery->close();

        //cartea exista => o sterge
        $deleteBookQuery = $conn->prepare("DELETE FROM book WHERE book_id = ?");
        $deleteBookQuery->bind_param("i", $bookId);
        $deleteBookQueryResult = $deleteBookQuery->execute();
        $deleteBookQuery->close();

        if ($deleteBookQueryResult) {
            http_response_code(200);
            echo json_encode(array("status" => "success", "message" => "Book deleted successfully"));
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(array("status" => "error", "message" => "Error deleting book"));
        }
    } else {
        $checkBookQuery->close();
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "Book not found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only DELETE method is allowed"));
}
?>
