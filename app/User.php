<?php
namespace App;
class User
{
    private $phone;
    private $password;
    private $name;
    private $surname;
    private $array;
    public function __construct(){
    }
    public function user_login($login,$password){
        $db = new DBConnection();
        try{
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT * FROM user WHERE phone_number = :phone AND password = :password");
            $stmt->bindParam(':phone', $login);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch();
            $this->registrateSession($user['id']);
            /*if (!$this->isSessionExist($user['id'])){
                $this->registrateSession($user['id']);
            }*/
            return 0;

        }catch (PDOException $e){
            echo "Error Login " . $e->getMessage();
        }finally {
            if ($con) {
                $conn = null;
            }
        }
    }
    public function registration($data){
    $db = new DBConnection();
        try {
            $conn = $db->startConnection();
            if (!$this->isAccountExist($data["phone"])) {
                $stmt = $conn->prepare("INSERT INTO user (phone_number, password, name, surname) VALUES (:phone, :password, :name, :surname)");
                $stmt->bindParam(':phone', $data["phone"]);
                $stmt->bindParam(':password', $data["password"]);
                $stmt->bindParam(':name', $data["name"]);
                $stmt->bindParam(':surname', $data["surname"]);
                $stmt->execute();
                $response = array(
                    "status" => "done",
                    "registration" => "true",
                    "error" => "",

                );
                echo json_encode($response);
            }
            else{
                $response = array(
                    "status" => "done",
                    "registration" => "false",
                    "error" => "accaunt already exist",

                );
                echo json_encode($response);
            }
        } catch (PDOException $e) {
            echo "Error creating account: " . $e->getMessage();
        } finally {
            if ($conn) {
                $conn = null;
            }
        }
}

    public function getCompanyList($token){
        $db = new DBConnection();
        $id = $this->getUserId($token);
        try{
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT * FROM company WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $id);
            $stmt->execute();
            $data = array();
            $i = 0;
            while($row = $stmt->fetch()){
                $data[$i]["company_id"] = $row["id"];
                $data[$i]["company_name"] = $row["company_name"];
                $data[$i]["company_adress"] = $row["address"];
                $i++;
            }
            echo json_encode($data);
        }catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function registrateSession($id){
        $db = new DBConnection();
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+10 day'));
        try{
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO session_token (user_id, token, expiration) VALUES (:user_id, :token, :expiration)");
            $stmt->bindParam(':user_id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
            $stmt->bindParam(':expiration', $expiration, \PDO::PARAM_STR);
            $stmt->execute();
            $data = array(
                "status" => "true",
                "user_id" => $id,
                "session_token" => $token,

            );
            echo json_encode($data);
        }catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
        $conn = null;
    }
    public function getUserId($token){
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
    public function addCompany($sessionToken,$title,$address){
        $db = new DBConnection();
        $user_id = $this->getUserId($sessionToken);
        $company_id = uniqid("",false);
        echo $user_id." company ".$company_id;
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO company (id,user_id, company_name,address) VALUE (:id,:user_id, :company_name,:address)");
            $stmt->bindParam(':id',$company_id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':company_name',$title);
            $stmt->bindParam(':address',$address);
            $stmt->execute();
        }catch (PDOException $e){
            echo "Connection failed: " . $e->getMessage();
            return null;
        }

    }
    public function isAccountExist($phone){
        $db = new DBConnection();
        try {
            $conn = $db->startConnection();
            if ($conn) {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE phone_number = :phone");
                $stmt->bindParam(':phone', $phone);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                return $count > 0;
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

}

