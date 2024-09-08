<?php

namespace App;

class UserRepository
{
    private $sessionToken;
    private $dbConnection;
    public function __construct()
    {
        $db = DBConnection::getInstance();
        $this->dbConnection = $db->getConnection();
    }
    public function authorization ($userData){
        try {
            $stmt = $this->dbConnection->prepare("SELECT * FROM user WHERE phone_number = :phone_number AND password = :password");
            $stmt->execute($userData);
            $count = $stmt->fetchColumn();
            if($count > 0){
                $sessionToken = $this->registrateSession($userData["phone_number"]);
                return $sessionToken;
            }else{
                return false;
            }
           

        } 
        catch (PDOException $e) {
            echo "Error Login " . $e->getMessage();
            return null;
        } 
    }
    private function getUserIdByPhone ($phone){
        $stmt = $this->dbConnection->prepare("SELECT id FROM user WHERE phone_number = :phone_number");
        $stmt->bindParam("phone_number", $phone);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user["id"];
    }
    public function registrateSession($phone)
    {
        $id = $this->getUserIdByPhone($phone);
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+10 day'));
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO session_token (user_id, token, expiration) VALUES (:user_id, :token, :expiration)");
            $stmt->bindParam(':user_id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
            $stmt->bindParam(':expiration', $expiration, \PDO::PARAM_STR);
            $stmt->execute();
            return $token;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
        $conn = null;
    }
    public function createAccount($userData)
    {
        try {
                $stmt = $this->dbConnection->prepare("INSERT INTO user (phone_number, password, name, surname) VALUES (:phone_number, :password, :name, :surname)");
                $stmt->execute($userData);
                return true;
            }  catch (PDOException $e) {
            echo "Error creating account: " . $e->getMessage();
            exit();
        }
    }
    public function isAccountExist($phone){
        try {
                $stmt = $this->dbConnection->prepare("SELECT COUNT(*) FROM user WHERE phone_number = :phone");
                $stmt->bindParam(':phone', $phone);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                return $count > 0;
        } catch (PDOException $e) {
            echo "Error checking account existence: " . $e->getMessage();
            return false;
        } 
    }
}