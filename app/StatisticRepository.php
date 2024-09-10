<?php
namespace App;

class StatisticRepository extends \App\Repository{
    public function getCheckList($data)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT * FROM `check` WHERE company_id = :company_id AND traiding_point_id = :traiding_point_id");
            $trade_point = 1;
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':traiding_point_id', $trade_point);
            $stmt->execute();
            $result = array();
            $i = 0;
            while ($row = $stmt->fetch()) {
                $result[$i]["status"] = $row["status"];
                $result[$i]["time"] = $row["time"];
                $result[$i]["cash_pay"] = $row["cash_pay"];
                $result[$i]["card_pay"] = $row["card_pay"];
                $result[$i]["final_price"] = $row["final_price"];
                $i++;
            }
            return($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getCheckListDetailed($data)
    {
        try {
            $company_id = $data['company_id'];
            $sql = "SELECT c.id, c.status, c.client_id, CONCAT(c.date, ' ', c.time) AS datetime, c.cash_pay, c.card_pay, c.bonus_pay, c.promotion_id, c.discount_id, c.discount_sum, c.final_price 
        FROM `check` c 
        WHERE c.company_id = :company_id";

            $params = [':company_id' => $company_id];

            if (isset($data['filters']['date']['from']) && isset($data['filters']['date']['to'])) {
                $fromDate = $data['filters']['date']['from'];
                $toDate = $data['filters']['date']['to'];

                $sql .= " AND DATE(c.date) BETWEEN :from_date AND :to_date";
                $params[':from_date'] = $fromDate;
                $params[':to_date'] = $toDate;
            }

            $stmt = $this->dbConnection->prepare($sql);
            $stmt->execute($params);
            $checks = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
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
                    'self_price' => 0,
                    'final_price' => $row['final_price'],
                    'products' => array()
                );
                $productSql = "
    SELECT 
        s.product_id, 
        s.type, 
        s.amount, 
        s.tradespot, 
        s.discount,
        s.self_price, 
        s.price, 
        p.title
    FROM 
        sales s
    JOIN 
        product p ON s.product_id = p.id
    WHERE 
        s.document_id = :check_id AND 
        s.company_id = :company_id
";
                $productStmt = $this->dbConnection->prepare($productSql);
                $productStmt->bindParam(':check_id', $row['id']);
                $productStmt->bindParam(':company_id', $company_id);
                $productStmt->execute();
                while ($productRow = $productStmt->fetch(\PDO::FETCH_ASSOC)) {
                    $product = array(
                        'product_id' => $productRow['product_id'],
                        'type' => $productRow['type'],
                        'title' => $productRow['title'],
                        'amount' => $productRow['amount'],
                        'tradespot' => $productRow['tradespot'],
                        'discount' => $productRow['discount'],
                        'self_price' => $productRow['self_price'],
                        'self_price_sum' => $productRow['self_price'] * $productRow['amount'],
                        'price' => $productRow['price'],
                        'price_sum' => $productRow['price'] * $productRow['amount'],

                    );
                    $check['products'][] = $product;
                    $check['self_price'] += $product["amount"] * $product['self_price'];
                }
                $checks[] = $check;
            }
            $response = array('checks' => $checks);
            return $response;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}