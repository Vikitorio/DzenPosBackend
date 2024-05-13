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
    public function checkList($data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $token = $data['token'];
            $company_id = $data['company_id'];
            $sql = "SELECT c.id, c.status, c.client_id, CONCAT(c.date, ' ', c.time) AS datetime, c.cash_pay, c.card_pay, c.bonus_pay, c.promotion_id, c.discount_id, c.discount_sum, c.final_price 
                FROM `check` c 
                WHERE c.company_id = :company_id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':company_id', $company_id);
            $stmt->execute();
            $checks = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $check = array(
                    'id' => $row['id'],
                    'status' => $row['status'],
                    'client_id' => $row['client_id'],
                    'datetime' => $row['datetime'],
                    'cash_pay' => $row['cash_pay'],
                    'card_pay' => $row['card_pay'],
                    'bonus_pay' => $row['bonus_pay'],
                    'promotion_id' => $row['promotion_id'],
                    'discount_id' => $row['discount_id'],
                    'discount_sum' => $row['discount_sum'],
                    'final_price' => $row['final_price'],
                    'products' => array()
                );
                $productSql = "SELECT product_id, type, amount, tradespot, discount, price 
                           FROM sales 
                           WHERE document_id = :check_id AND company_id = :company_id";
                $productStmt = $con->prepare($productSql);
                $productStmt->bindParam(':check_id', $row['id']);
                $productStmt->bindParam(':company_id', $company_id);
                $productStmt->execute();
                while ($productRow = $productStmt->fetch(PDO::FETCH_ASSOC)) {
                    $product = array(
                        'product_id' => $productRow['product_id'],
                        'type' => $productRow['type'],
                        'amount' => $productRow['amount'],
                        'tradespot' => $productRow['tradespot'],
                        'discount' => $productRow['discount'],
                        'price' => $productRow['price']
                    );
                    $check['products'][] = $product;
                }
                $checks[] = $check;
            }
            $response = array('checks' => $checks);
            echo json_encode($response);
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