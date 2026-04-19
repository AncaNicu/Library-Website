login:
<?php
require_once('../includes/db_config.php');
session_start();

header('Content-Type: application/json');

// Handle POST request for user login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $enteredPassword = $data['user_password'];

    // Check if username exists
    $checkStmt = $conn->prepare("SELECT user_id, username, user_email, user_password, user_role FROM user WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Username exists, verify the password
        $checkStmt->bind_result($userId, $username, $userEmail, $storedPassword, $userRole);
        $checkStmt->fetch();

        if (password_verify($enteredPassword, $storedPassword)) {
            // Set session variables
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            // Additional logic based on user role
            if ($userRole === 'admin') {
                http_response_code(200);
                echo json_encode(array("message" => "Login successful", "user_id" => $userId, "username" => $username, "user_email" => $userEmail, "user_role" => $userRole, "redirect_url" => '../admin_area/home.php'));
            } else {
                http_response_code(200);
                echo json_encode(array("message" => "Login successful", "user_id" => $userId, "username" => $username, "user_email" => $userEmail, "user_role" => $userRole, "redirect_url" => 'home.php'));
            }
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Incorrect password"));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "User not found"));
    }

    $checkStmt->close();
    $conn->close();
}
?>




regoister:
<?php
require_once('../includes/db_config.php');

header('Content-Type: application/json');

// Handle POST request for user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $user_email = $data['user_email'];
    $user_password = password_hash($data['user_password'], PASSWORD_DEFAULT);
    $user_phone = $data['user_phone'];
    $user_address = $data['user_address'];
    $user_role = 'user'; // Set user_role to 'user'

    // Check if username or email already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM user WHERE username = ? OR user_email = ?");
    $checkStmt->bind_param("ss", $username, $user_email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        http_response_code(401); // HTTP 401 Unauthorized
        echo json_encode(array("message" => "Username or email already exists. Please choose another one."));
    } else {
        $checkStmt->close();

        // Insert new user
        $insertStmt = $conn->prepare("INSERT INTO user (username, user_email, user_password, user_phone, user_address, user_role) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $username, $user_email, $user_password, $user_phone, $user_address, $user_role);

        if ($insertStmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "User registered successfully"));
        } else {
            http_response_code(401); // HTTP 401 Unauthorized
            echo json_encode(array("message" => "User registration failed"));
        }

        $insertStmt->close();
    }

    $conn->close();
}
?>
