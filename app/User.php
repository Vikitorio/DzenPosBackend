<?php
namespace App;
class User
{
    private $sessionToken;
    public function authorization($data)
    {
        $userRep = new UserRepository();
        $result = [];
        $token = $userRep->authorization($data);
        if ($token) {
            $result = [
                "status" => "success",
                "token" => $token,
            ];
        } else {
            $result = [
                "status" => "failure",
                "error" => "Invalid data"
            ];
        }
    
        echo json_encode($result);
    }
    public function registration($data)
    {
        $userRep = new UserRepository();
        $userData = [
            "phone_number" => $data["phone_number"],
            "password" => $data["password"],
            "name" => $data["name"],
            "surname" => $data["surname"],
        ];
        $response = array(
            "status" => "",
            "error" => "",

        );
        if ($userRep->isAccountExist($userData["phone_number"])) {
            $response["status"] = "error";
            $response["status_code"] = "0";
            $response["error"] = "Account already exists";
        } else {
            $result = $userRep->createAccount($userData);
            if ($result === true) {
                $response["status"] = "success";
            } else {
                $response["status"] = "error";
                $response["error"] = $result;
            }
        }
        if ($response["status"] === "success") {
            $response["token"] = $userRep->registrateSession($userData["phone_number"]);
        }
        echo json_encode($response);
    }




    public function getCompanyList($token)
    {
        $db = new DBConnection();
        $id = $this->getUserId($token);
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT * FROM company WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $id);
            $stmt->execute();
            $data = array();
            $i = 0;
            while ($row = $stmt->fetch()) {
                $data[$i]["company_id"] = $row["id"];
                $data[$i]["company_name"] = $row["company_name"];
                $data[$i]["company_adress"] = $row["address"];
                $i++;
            }
            echo json_encode($data);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
   
    public function getUserIdByToken($token)
    {
        $db = new DBConnection();
        try {
            $conn = $db->startConnection();
            if ($conn) {
                $stmt = $conn->prepare("SELECT user_id FROM session_token WHERE token = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();
                $user = $stmt->fetch();
                return $user["user_id"];
            }
            return false;
        } catch (PDOException $e) {
            echo "Error checking account existence: " . $e->getMessage();
            return false;
        } finally {
            if ($conn) {
                $conn = null;
            }
        }
    }
    public function addCompany($sessionToken, $title, $address)
    {
        $db = new DBConnection();
        $user_id = $this->getUserId($sessionToken);
        $company_id = uniqid("", false);
        echo $user_id . " company " . $company_id;
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO company (id,user_id, company_name,address) VALUE (:id,:user_id, :company_name,:address)");
            $stmt->bindParam(':id', $company_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':company_name', $title);
            $stmt->bindParam(':address', $address);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }

    }


}

