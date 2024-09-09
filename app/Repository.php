<?php
namespace App;

abstract class Repository {
    protected $dbConnection;
    use RepositoryDBConnection;

    public function __construct() {
        $db = DBConnection::getInstance();
        $this->dbConnection = $db->getConnection();
    }
}
