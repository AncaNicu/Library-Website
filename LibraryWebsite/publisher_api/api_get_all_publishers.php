<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip GET inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //obtine toate editurile din baza de date
    $getAllPublishersQuery = $conn->prepare("SELECT publisher_id, publisher_name FROM publisher");
    $getAllPublishersQuery->execute();
    $result = $getAllPublishersQuery->get_result();
    $publishers = $result->fetch_all(MYSQLI_ASSOC);
    $getAllPublishersQuery->close();

    //verifica daca exista edituri in bd
    if (!empty($publishers)) {
        http_response_code(200);
        echo json_encode(array("status" => "success", "publishers" => $publishers));
    } else {
        http_response_code(404); // Not Found
        echo json_encode(array("status" => "error", "message" => "No publishers found"));
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Only GET method is allowed"));
}
?>
