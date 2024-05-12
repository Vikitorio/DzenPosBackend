<?php


class Company
{
    public function addProduct($userId,$data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO product (company_id,title,type,category_id,tax_id,description,cost,selling_price,quantity) VALUE (:company_id,:title,:type,:category_id,:tax_id,:description,:cost,:selling_price,:quantity)");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':title', $data["title"]);
            $stmt->bindParam(':type', $data["type"]);
            $stmt->bindParam(':category_id', $data["category_id"]);
            $stmt->bindParam(':tax_id', $data["tax_id"]);
            $stmt->bindParam(':description', $data["description"]);
            $stmt->bindParam(':cost', $data["cost"]);
            $stmt->bindParam(':selling_price', $data["selling_price"]);
            $stmt->bindParam(':quantity', $data["quantity"]);
            $stmt->execute();
            $result = array();
            $i = 0;
            $i = 0;
            while ($row = $stmt->fetch()) {
                $result[$i]["company_id"] = $row["company_id"];
                $result[$i]["id"] = $row["id"];
                $result[$i]["trading_point_id"] = $row["trading_point_id"];
                $result[$i]["status"] = $row["status"];
                $result[$i]["worker_id"] = $row["worker_id"];
                $result[$i]["client_id"] = $row["client_id"];
                $result[$i]["date"] = $row["date"];
                $result[$i]["time"] = $row["time"];
                $result[$i]["cash_pay"] = $row["cash_pay"];
                $result[$i]["card_pay"] = $row["card_pay"];
                $result[$i]["bonus_pay"] = $row["bonus_pay"];
                $result[$i]["promotion_id"] = $row["promotion_id"];
                $result[$i]["discount_id"] = $row["discount_id"];
                $result[$i]["discount_sum"] = $row["discount_sum"];
                $result[$i]["final_price"] = $row["final_price"];
                $i++;
            }
            echo json_encode($result);

        }catch (PDOException $e){
            echo "Connection failed: " . $e->getMessage();
            return null;
        }

}
    public function makeCheck($data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO `check` (company_id, traiding_point_id, status, worker_id, client_id, cash_pay, card_pay, bonus_pay, promotion_id, discount_id, discount_sum, final_price) 
                               VALUES (:company_id, :traiding_point_id, :status, :worker_id, :client_id, :cash_pay, :card_pay, :bonus_pay, :promotion_id, :discount_id, :discount_sum, :final_price)");
            $stmt->bindParam(':company_id', $data["company_id"]);
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
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getCheckList($data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT * FROM `check` WHERE company_id = :company_id AND traiding_point_id = :traiding_point_id");
            $trade_point = 1;
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':traiding_point_id', $trade_point);
            $stmt->execute();
            $result = array();
            $i = 0;
            while($row = $stmt->fetch()){
                $result[$i]["status"] = $row["status"];
                $result[$i]["time"] = $row["time"];
                $result[$i]["cash_pay"] = $row["cash_pay"];
                $result[$i]["card_pay"] = $row["card_pay"];
                $result[$i]["final_price"] = $row["final_price"];
                $i++;
            }
            echo json_encode($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getProductList($data){
        $db = new DBConnection();
        try{
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT * FROM product WHERE company_id = :company_id");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->execute();
            $result = array();
            $i = 0;
            while($row = $stmt->fetch()){
                $result[$i]["title"] = $row["title"];
                $result[$i]["type"] = $row["type"];
                $result[$i]["category_id"] = $row["category_id"];
                $result[$i]["description"] = $row["description"];
                $result[$i]["cost"] = $row["cost"];
                $result[$i]["selling_price"] = $row["selling_price"];
                $result[$i]["quantity"] = $row["quantity"];
                $i++;
            }
            echo json_encode($result);
        }catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

}