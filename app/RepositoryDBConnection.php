<?php
namespace App;

trait RepositoryDBConnection {
    public function setConnection(){
        $db = DBConnection::getInstance();
        $this->dbConnection = $db->getConnection();
    }
}

?>