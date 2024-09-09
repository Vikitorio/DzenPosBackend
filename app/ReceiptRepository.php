<?php
namespace App;

class ReceiptRepository extends \App\Repository{
    private $db;

    public function makeCheck($checkData){
        try {
            unset($checkData["products"]);
            $stmt = $this->dbConnection->prepare("INSERT INTO `check` (company_id, traiding_point_id, status, worker_id, client_id, cash_pay, card_pay, bonus_pay, promotion_id, discount_id, discount_sum, final_price) 
                               VALUES (:company_id, :traiding_point_id, :status, :worker_id, :client_id, :cash_pay, :card_pay, :bonus_pay, :promotion_id, :discount_id, :discount_sum, :final_price)");
            $stmt->execute($checkData);
            $lastId = $this->dbConnection->lastInsertId();

            return $lastId;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}