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

            return $conn; // Return the connection object for later use
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null; // Return null to indicate connection failure
        }
    }
    public function isAccountExist($phone){
        try {
            // Start a new database connection
            $conn = $this->startConnection();

            if ($conn) {
                // Prepare the SQL statement
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE phone_number = :phone");

                // Bind parameters
                $stmt->bindParam(':phone', $phone);

                // Execute the query
                $stmt->execute();

                // Fetch the result
                $count = $stmt->fetchColumn();

                // Check if an account with the given phone number exists
                return $count > 0;
            }

            return false;
        } catch (PDOException $e) {
            echo "Error checking account existence: " . $e->getMessage();
            return false;
        } finally {
            // Close the connection
            if ($conn) {
                $conn = null;
            }
        }
    }
    public function createAccount($phone, $password, $name = null, $surname = null){
        try {
            // Start a new database connection
            $conn = $this->startConnection();

            if ($conn) {
                // Prepare the SQL statement
                $stmt = $conn->prepare("INSERT INTO users (phone_number, password, name, surname) VALUES (:phone, :password, :name, :surname)");

                // Bind parameters
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);

                // Execute the query
                $stmt->execute();

                echo "Account created successfully";
            }
        } catch (PDOException $e) {
            echo "Error creating account: " . $e->getMessage();
        } finally {
            // Close the connection
            if ($conn) {
                $conn = null;
            }
        }
    }
}

?>