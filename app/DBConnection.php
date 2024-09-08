<?php

namespace App;

final class DBConnection
{
    private static ?self $instance = null;
    private $conn = null;

    private string $user = 'root';
    private string $serverName = 'localhost';
    private string $password = '';
    private string $dbName = 'testdb';

    private function __construct()
    {
        try {
            $this->conn = new \PDO("mysql:host={$this->serverName};dbname={$this->dbName}", $this->user, $this->password);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?\PDO
    {
        return $this->conn;
    }

    public function isSessionExist($id): bool
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM session_token WHERE user_id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
    public function __clone(): void
    {
    }
    public function __wakeup(): void
    {
    }

}

?>