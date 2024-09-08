<?php

class CheckRepository{
    private $db;

    public function makeCheck($data){
        $company = $data["company_id"];
        $data = $data["check"];
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO `check` (company_id, traiding_point_id, status, worker_id, client_id, cash_pay, card_pay, bonus_pay, promotion_id, discount_id, discount_sum, final_price) 
                               VALUES (:company_id, :traiding_point_id, :status, :worker_id, :client_id, :cash_pay, :card_pay, :bonus_pay, :promotion_id, :discount_id, :discount_sum, :final_price)");
            $stmt->bindParam(':company_id', $company);
            $stmt->bindParam(':traiding_point_id', $data["traiding_point_id"]);
            $stmt->bindParam(':status', $data["status"]);
            $stmt->bindParam(':worker_id', $data["worker_id"]);
            $stmt->bindParam(':client_id', $data["client_id"]);
            $stmt->bindParam(':cash_pay', $data["cash_pay"]);
            $stmt->bindParam(':card_pay', $data["card_pay"]);
            $stmt->bindParam(':bonus_pay', $data["bonus_pay"]);
            $stmt->bindParam(':promotion_id', $data["promotion_id"]);
            $stmt->bindParam(':discount_id', $data["discount_id"]);
            $stmt->bindParam(':discount_sum', $data["discount_sum"]);
            $stmt->bindParam(':final_price', $data["final_price"]);

            $stmt->execute();
            $lastId = $con->lastInsertId();
            $this->makeSaleRecords($lastId,$company, $data["products"] );
            $result = array();
            $result["status"]="done";
            echo json_encode($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}