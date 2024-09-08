<?php
use App\User;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once('vendor/autoload.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Stop the PHP algorithm execution
    exit();
}
$requestC = explode("/",$_SERVER["REQUEST_URI"]);
$dbConnection = new \App\DBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';


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
        $user = new \App\User();
        $user->user_login($data["phone"],$data["password"]);
        break;
    case "company":
        echo 'company';
        break;
    case "add_company":
        $user = new \App\User();
        $user->addCompany($data["token"],$data["title"],$data["address"]);
        break;
    case "company_list":
        $user = new \App\User();
        $user->getCompanyList($data["token"]);
        break;
    case "registration":
        $user = new \App\User();
        $user->registration($data);
        break;
    case "add_product":
        $user = new \App\User();
        $company = new \App\Product();
        $company->addProduct($userId = 1,$data);
        break;
    case "get_product":
        $user = new \App\User();
       // $userId = $user->getUserId($data["token"]);
        $company = new \App\Product();
        $company->getProduct($data);
        break;
    case "make_check":
        $user = new \App\User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new \App\Warehouse();
        $warehouse->makeCheck($data);
        break;
    case "checks":
        $user = new \App\User();
        $userId = $user->getUserId($data["token"]);
        $company = new \App\Product();
        $company->getCheckList($data);
        break;
    case "write_off":
        $user = new \App\User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new Warehouse();
        $warehouse->makeWriteOffDocument($data);
        break;
    case "make_arrival":
        $user = new \App\User();
        $userId = $user->getUserId($data["token"]);
        $warehouse = new \App\Warehouse();
        $warehouse->makeArrivalDocument($data);
        break;
    case "check":
        $user = new \App\User();
        $userId = $user->getUserId($data["token"]);
        if($requestC[5] == "all"){
            $statistic = new \App\CompanyStatistic();
            $statistic->checkList($data);
        }
        break;
    case "get_write_off":
        $warehouse = new \App\Warehouse();
        $warehouse->getWriteOffDocuments($data["company_id"]);
        break;
    case "get_arrivals":
        $statistic = new \App\CompanyStatistic();
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
