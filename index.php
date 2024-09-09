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
    exit();
}
$requestC = explode("/", $_SERVER["REQUEST_URI"]);
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

switch ($requestC[4]) {
    case "login":
        $user = new User();
        $user->authorization($data);
        break;
    case "registration":
        $user = new User();
        $user->registration($data);
        break;
    case "add_company":
        $company = new \App\Company();
        $company->addCompany($data);
        break;
    case "company_list":
        $company = new \App\Company();
        $company->getCompanyList($data);
        break;

    case "add_product":
        $user = new User();
        $product= new \App\Product();
        $product->addProduct($data);
        break;
    case "get_product":
        $user = new User();
        // $userId = $user->getUserId($data["token"]);
        $product = new \App\Product();
        $product->getProduct($data);
        break;
    case "make_check":
        $receipt = new \App\Receipt();
        $receipt->makeCheck($data);
        break;
    case "checks":
        $user = new User();
        $userId = $user->getUserId($data["token"]);
        $company = new \App\Product();
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
        if ($requestC[5] == "all") {
            $statistic = new \App\CompanyStatistic();
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



?>