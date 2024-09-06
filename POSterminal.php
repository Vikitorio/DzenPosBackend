<?php


class POSterminal
{
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
    public function makeSaleRecords($checkId, $companyId, $products){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO sales (document_id, product_id, company_id, type, amount, self_price, tradespot, discount, price, time) 
                           VALUES (:document_id, :product_id, :company_id, :type, :amount, :self_price, :tradespot, :discount, :price, NOW())");

            foreach ($products as $product) {
                $stmt->bindParam(':document_id', $checkId);
                $stmt->bindParam(':product_id', $product['product_id']);
                $stmt->bindParam(':company_id', $companyId);
                $stmt->bindParam(':type', $product['type']);
                $stmt->bindParam(':amount', $product['amount']);
                $stmt->bindParam(':self_price', $product['self_price']);
                $stmt->bindParam(':tradespot', $product['tradespot']);
                $stmt->bindParam(':discount', $product['discount']);
                $stmt->bindParam(':price', $product['price']);
                $stmt->execute();
                $this->updateQuantity($product['product_id'], -$product['amount'], $companyId);
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}