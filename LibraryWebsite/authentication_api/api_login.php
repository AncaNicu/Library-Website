<?php
require_once('../includes/db_config.php');
session_start();

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca cererea primita este de tip POST inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    //ia datele primite
    $username = $data['username'];
    $enteredPassword = $data['user_password'];

    //verifica daca utilizatorul exista
    $checkStmt = $conn->prepare("SELECT user_id, username, user_email, user_password, user_role FROM user WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        //exista => verifica daca parola e corecta
        $checkStmt->bind_result($userId, $username, $userEmail, $storedPassword, $userRole);
        $checkStmt->fetch();

        if (password_verify($enteredPassword, $storedPassword)) {
            // seteaza variabilele sesiunii curente
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            //date trimise catre login.php in functie de rolul utilizatorului
            $response = array(
                "status" => "success",
                "message" => "Login successful",
                "user_id" => $userId,
                "username" => $username,
                "user_email" => $userEmail,
                "user_role" => $userRole,
                "redirect_url" => ($userRole === 'admin') ? '../admin_area/home.php' : 'home.php'
            );
            http_response_code(200);
            echo json_encode($response);
        } else {
            http_response_code(401);
            echo json_encode(array("status" => "error", "message" => "Incorrect password"));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("status" => "error", "message" => "User not found"));
    }

    $checkStmt->close();
    $conn->close();
}
?>
