<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'DBConnection.php';
include 'User.php';
include 'Company.php';
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new DBConnection();
$data = json_decode(file_get_contents('php://input'),true);
switch ($requestC[4]){
    case "login":
        $user = new User();
        $user->user_login($data["phone"],$data["password"]);
        break;
    case "company":
        echo 'company';
        break;
    case "add_company":
        $user = new User();
        $user->addCompany($data["token"],$data["title"],$data["address"]);
        break;
    case "company_list":
        $user = new User();
        $user->getCompanyList($data["token"]);
        break;
    case "registration":
        $user = new User();
        $user->registration($data);
        break;
    case "add_product":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->addProduct($userId,$data);
        break;
    case "get_product":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->getProductList($data);
        break;
    case "make_check":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->makeCheck($data);
        break;
    case "checks":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->getCheckList($data);
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