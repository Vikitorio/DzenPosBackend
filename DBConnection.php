<?php

class DBConnection
{
    private $user = 'root';
    private $serverName = 'localhost';
    private $password = '';
    private $dbName = 'testdb';

    public function startConnection(){
        try {
            $conn = new PDO("mysql:host={$this->serverName};dbname={$this->dbName}", $this->user, $this->password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function isSessionExist($id){
        try {
            $con = $this->startConnection();
            $stmt = $con->prepare("SELECT COUNT(*) FROM session_token WHERE user_id = :id");
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;

        } catch (PDOException $e) {
            echo "failed: " . $e->getMessage();
        }
    }
    public function authorization($login, $password){
        try{
            $con = $this->startConnection();
            $stmt = $con->prepare("SELECT * FROM user WHERE phone_number = :phone AND password = :password");
            $stmt->bindParam(':phone', $login);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch();
            $this->registrateSession($user['id']);
            /*if (!$this->isSessionExist($user['id'])){
                $this->registrateSession($user['id']);
            }
            */
            return 0;

        }catch (PDOException $e){
            echo "Error Login " . $e->getMessage();
        }finally {
            if ($con) {
                $conn = null;
            }
    }}
    public function isAccountExist($phone){
        try {
            $conn = $this->startConnection();
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
    public function createAccount($phone, $password, $name = null, $surname = null){
        try {
            $conn = $this->startConnection();
            if ($conn && !$this->isAccountExist($phone)) {
                $stmt = $conn->prepare("INSERT INTO user (phone_number, password, name, surname) VALUES (:phone, :password, :name, :surname)");
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->execute();

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
    public function registrateSession($id){
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+10 day'));
        try{
            $con = $this->startConnection();
            $stmt = $con->prepare("INSERT INTO session_token (user_id, token, expiration) VALUES (:user_id, :token, :expiration)");
            $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':expiration', $expiration, PDO::PARAM_STR);
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
    public function addCompani($id,$sessionToken){

    }
}

?>