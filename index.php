<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'DBConnection.php';
include 'User.php';
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new DBConnection();
$data = json_decode(file_get_contents('php://input'));
$data = (array) $data;
$user = new User(...$data);
echo $user->getUserInfo();
/*

    if (isset($_POST['phone']) && isset($_POST['password'])) {
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $surname = isset($_POST['surname']) ? $_POST['surname'] : null;

        // Check if the account already exists
        if ($dbConnection->isAccountExist($phone)) {
            // Return error response
            $response = array('status' => 'error', 'message' => 'Account already exists.');
            http_response_code(400);
            echo json_encode($response);
        } else {
            // Create the account
            $dbConnection->createAccount($phone, $password, $name, $surname);

            // Return success response
            $response = array('status' => 'success', 'message' => 'Account created successfully.');
            echo json_encode($response);
        }
    } else {
        // Return error response for missing parameters
        $response = array('status' => 'error', 'message' => 'Missing parameters.');
        http_response_code(400);
        echo json_encode($response);
    }

*/

?>