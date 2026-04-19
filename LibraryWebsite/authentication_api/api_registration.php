<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip POST inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $username = $data['username'];
    $user_email = $data['user_email'];
    $user_password = password_hash($data['user_password'], PASSWORD_DEFAULT);
    $user_phone = $data['user_phone'];
    $user_address = $data['user_address'];
    $user_role = 'user'; // Set user_role to 'user'

    //verifica daca username-ul sau email-ul exista deja
    $checkStmt = $conn->prepare("SELECT user_id FROM user WHERE username = ? OR user_email = ?");
    $checkStmt->bind_param("ss", $username, $user_email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        http_response_code(401); // HTTP 401 Unauthorized
        echo json_encode(array("status" => "error", "message" => "Username or email already exists. Please choose another one."));
    } else {
        $checkStmt->close();

        //insereaza noul utilizator in baza de date
        $insertStmt = $conn->prepare("INSERT INTO user (username, user_email, user_password, user_phone, user_address, user_role) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $username, $user_email, $user_password, $user_phone, $user_address, $user_role);

        if ($insertStmt->execute()) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "message" => "User registered successfully"));
        } else {
            http_response_code(401); // HTTP 401 Unauthorized
            echo json_encode(array("status" => "error", "message" => "User registration failed"));
        }

        $insertStmt->close();
    }

    $conn->close();
}
?>
