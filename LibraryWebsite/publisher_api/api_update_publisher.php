<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip PUT inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $publisherName = $data['publisher_name'];
    $newPublisherName = $data['new_publisher_name'];

    //verifica daca editura exista
    $checkPublisherQuery = $conn->prepare("SELECT publisher_id FROM publisher WHERE publisher_name = ?");
    $checkPublisherQuery->bind_param("s", $publisherName);
    $checkPublisherQuery->execute();
    $checkPublisherQuery->store_result();

    if ($checkPublisherQuery->num_rows > 0) {
        $checkPublisherQuery->bind_result($publisherId);
        $checkPublisherQuery->fetch();
        $checkPublisherQuery->close();

        //exista => verifica daca noua editura exista
        $checkNewPublisherQuery = $conn->prepare("SELECT publisher_id FROM publisher WHERE publisher_name = ?");
        $checkNewPublisherQuery->bind_param("s", $newPublisherName);
        $checkNewPublisherQuery->execute();
        $checkNewPublisherQuery->store_result();

        if ($checkNewPublisherQuery->num_rows == 0) {
            $checkNewPublisherQuery->close();

            //nu exista => actualizeaza editura
            $updatePublisherQuery = $conn->prepare("UPDATE publisher SET publisher_name = ? WHERE publisher_id = ?");
            $updatePublisherQuery->bind_param("si", $newPublisherName, $publisherId);

            if ($updatePublisherQuery->execute()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Publisher updated successfully"));
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(array("status" => "error", "message" => "Error updating publisher"));
            }
        } else {
            $checkNewPublisherQuery->close();
            http_response_code(400); // Bad Request
            echo json_encode(array("status" => "error", "message" => "New publisher already exists"));
        }
    } else {
        $checkPublisherQuery->close();
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "Publisher not found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only PUT method is allowed"));
}
?>
