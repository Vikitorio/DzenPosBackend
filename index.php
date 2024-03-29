<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'DBConnection.php';
include 'User.php';
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new DBConnection();
$data = json_decode(file_get_contents('php://input'));
$user = new User($data);
$user->getUserInfo();
$user->registration();
/*

*/

?>