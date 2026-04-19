<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip DELETE inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    $publisherName = $data['publisher_name'];

    //daca e specificata editura de sters
    if (!empty($publisherName)) {
        //verif daca editura exista
        $checkPublisherQuery = $conn->prepare("SELECT publisher_id FROM publisher WHERE publisher_name = ?");
        $checkPublisherQuery->bind_param("s", $publisherName);
        $checkPublisherQuery->execute();
        $checkPublisherQuery->store_result();

        if ($checkPublisherQuery->num_rows > 0) {
            $checkPublisherQuery->bind_result($publisherId);
            $checkPublisherQuery->fetch();
            $checkPublisherQuery->close();

            //exista => sterge cartile casociate editurii
            $deleteBooksQuery = $conn->prepare("DELETE FROM book WHERE publisher_id = ?");
            $deleteBooksQuery->bind_param("i", $publisherId);
            $deleteBooksQueryResult = $deleteBooksQuery->execute();
            $deleteBooksQuery->close();

            //sterge si editura
            $deletePublisherQuery = $conn->prepare("DELETE FROM publisher WHERE publisher_id = ?");
            $deletePublisherQuery->bind_param("i", $publisherId);
            $deletePublisherQueryResult = $deletePublisherQuery->execute();
            $deletePublisherQuery->close();

            if ($deleteBooksQueryResult && $deletePublisherQueryResult) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Publisher and associated books deleted successfully"));
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(array("status" => "error", "message" => "Error deleting publisher and associated books"));
            }
        } else {
            http_response_code(404); // Not Found
            echo json_encode(array("status" => "error", "message" => "Publisher not found"));
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("status" => "error", "message" => "Please provide a publisher name"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only DELETE method is allowed"));
}
?>
