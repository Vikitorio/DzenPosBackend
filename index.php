<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'DBConnection.php';
include 'User.php';
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new DBConnection();
$data = json_decode(file_get_contents('php://input'));
switch ($requestC[4]){
    case "login":
        $user = new User($data);
        $user->user_login();
        break;
    case "companies":
        echo 'companies';
        break;
    case "registration":
        $user = new User($data);
        $user->registration();
        break;
    default:
        echo "annexpected api";
}


/*
$user = new User($data);
$user->user_login();
$user = new User($data);
$user->getUserInfo();
$user->registration();
*/

?>