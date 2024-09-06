<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'DBConnection.php';
include 'User.php';
include 'Company.php';
include 'Warehouse.php';
include "CompanyStatistic.php";


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Stop the PHP algorithm execution
    exit();
}
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new DBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

/*if ($contentType === 'application/json') {
    // Handle JSON data
    $data = json_decode(file_get_contents('php://input'), true);
} elseif (strpos($contentType, 'multipart/form-data') !== false) {
    // Handle form data
    $data = array(
        'token' => $_POST['token'],
        'company_id' => $_POST['company_id'],
        'title' => $_POST['title'],
        'type' => $_POST['type'],
        'category_id' => $_POST['category_id'],
        'description' => $_POST['description'],
        'cost' => $_POST['cost'],
        'selling_price' => $_POST['selling_price'],
        'quantity' => $_POST['quantity']
    );
} else {
    $data = null;
}
*/

if (strpos($contentType, 'multipart/form-data') !== false) {
    // Handle form data
    $data = array(
        'token' => $_POST['token'],
        'company_id' => $_POST['company_id'],
        'title' => $_POST['title'],
        'type' => $_POST['type'],
        'category_id' => $_POST['category_id'],
        'description' => $_POST['description'],
        'cost' => $_POST['cost'],
        'selling_price' => $_POST['selling_price'],
        'quantity' => $_POST['quantity']
    );
} else {
    $data = json_decode(file_get_contents('php://input'), true);
}

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
        $company = new Company();
        $company->addProduct($userId = 1,$data);
        break;
    case "get_product":
        $user = new User();
       // $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->getProduct($data);
        break;
    case "make_check":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new Warehouse();
        $warehouse->makeCheck($data);
        break;
    case "checks":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new Company();
        $company->getCheckList($data);
        break;
    case "write_off":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new Warehouse();
        $warehouse->makeWriteOffDocument($data);
        break;
    case "make_arrival":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new Warehouse();
        $warehouse->makeArrivalDocument($data);
        break;
    case "check":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        if($requestC[5] == "all"){
            $statistic = new CompanyStatistic();
            $statistic->checkList($data);
        }
        break;
    case "get_write_off":
        $warehouse = new Warehouse();
        $warehouse->getWriteOffDocuments($data["company_id"]);
        break;
    case "get_arrivals":
        $statistic = new CompanyStatistic();
        $statistic->getArrivalDocuments($data["company_id"]);
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