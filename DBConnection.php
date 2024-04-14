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


}

?>