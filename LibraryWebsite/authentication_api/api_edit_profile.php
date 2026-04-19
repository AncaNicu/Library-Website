<?php
require_once('../includes/db_config.php');

//seteaza antetul HTTP pt a arata ca continutul returnat este in format JSON
header('Content-Type: application/json');

//verifica daca utilizatorul e logat
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // HTTP 401 Unauthorized
    echo json_encode(array("status" => "error", "message" => "User not authenticated"));
    exit();
}

$userId = $_SESSION['user_id'];

//verifica daca cererea primita este de tip POST inainte de a gestiona cererea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    //daca a fost apasat butonul 'update_profile'
    if (isset($data['update_profile'])) {
        //ia datele primite
        $user_phone = $data['user_phone'];
        $user_address = $data['user_address'];

        //actualizeaza adresa si telefonul
        $updateStmt = $conn->prepare("UPDATE user SET user_phone = ?, user_address = ? WHERE user_id = ?");
        $updateStmt->bind_param("ssi", $user_phone, $user_address, $userId);

        if ($updateStmt->execute()) {
            http_response_code(200);
            echo json_encode(array("status" => "success", "message" => "Profile updated successfully"));
        } else {
            http_response_code(400); // HTTP 400 Bad Request
            echo json_encode(array("status" => "error", "message" => "Profile update failed"));
        }

        $updateStmt->close();
    }

    // daca a fost apasat butonul 'change_password'
    if (isset($data['change_password'])) {
        //ia datele primite
        $old_password = $data['old_password'];
        $new_password = $data['new_password'];
        $confirm_password = $data['confirm_password'];

        //verif daca old password e corecta
        $checkPasswordStmt = $conn->prepare("SELECT user_password FROM user WHERE user_id = ?");
        $checkPasswordStmt->bind_param("i", $userId);
        $checkPasswordStmt->execute();
        $checkPasswordStmt->bind_result($stored_password);
        $checkPasswordStmt->fetch();
        $checkPasswordStmt->close();

        if (password_verify($old_password, $stored_password)) {

            //verif daca noua parola si confirmarea ei se potrivesc
            if ($new_password === $confirm_password) {
                //face hash pt noua parola
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                //actualizeaza noua parola
                $updatePasswordStmt = $conn->prepare("UPDATE user SET user_password = ? WHERE user_id = ?");
                $updatePasswordStmt->bind_param("si", $hashed_password, $userId);

                if ($updatePasswordStmt->execute()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "success", "message" => "Password changed successfully"));
                } else {
                    http_response_code(400); // HTTP 400 Bad Request
                    echo json_encode(array("status" => "error", "message" => "Password change failed"));
                }

                $updatePasswordStmt->close();
            } else {
                http_response_code(400); // HTTP 400 Bad Request
                echo json_encode(array("status" => "error", "message" => "New password and confirm password do not match"));
            }
        } else {
            http_response_code(400); // HTTP 400 Bad Request
            echo json_encode(array("status" => "error", "message" => "Incorrect old password"));
        }
    }

    $conn->close();
}
?>
