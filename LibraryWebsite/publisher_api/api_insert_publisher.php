<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip POST inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia numele editurii primit
    $newPublisherName = $data['new_publisher_name'];

    //verifica daca editura exista deja
    $checkPublisherQuery = $conn->prepare("SELECT publisher_id FROM publisher WHERE publisher_name = ?");
    $checkPublisherQuery->bind_param("s", $newPublisherName);
    $checkPublisherQuery->execute();
    $checkPublisherQuery->store_result();

    if ($checkPublisherQuery->num_rows == 0) {
        $checkPublisherQuery->close();

        //nu exista => o insereaza
        $insertPublisherQuery = $conn->prepare("INSERT INTO publisher (publisher_name) VALUES (?)");
        $insertPublisherQuery->bind_param("s", $newPublisherName);

        if ($insertPublisherQuery->execute()) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "message" => "Publisher inserted successfully"));
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(array("status" => "error", "message" => "Error inserting publisher"));
        }
    } else {
        $checkPublisherQuery->close();
        http_response_code(400); // Bad Request
        echo json_encode(array("status" => "error", "message" => "Publisher already exists"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only POST method is allowed"));
}
?>
