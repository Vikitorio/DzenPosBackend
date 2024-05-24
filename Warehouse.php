<?php

class Warehouse
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

    public function updateQuantity($productId, $quantity, $companyId){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("UPDATE product SET quantity = quantity + :quantity WHERE id = :productId AND company_id = :companyId");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':companyId', $companyId);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }


    public function makeWriteOffDocument($data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO write_off_doc (company_id, sum) 
                           VALUES (:company_id, :sum)");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':sum', $data["write_off"]["sum"]);
            $stmt->execute();
            $lastId = $con->lastInsertId();
            $this->makeWriteOffProducts($lastId, $data);
            $result = array();
            $result["status"]="done";
            echo json_encode($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function makeWriteOffProducts($documentId, $data){
        $db = new DBConnection();
        $products = $data["write_off"]["products"];
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO write_off_products (document_id, company_id, product_id, amount, cost) 
                           VALUES (:document_id, :company_id, :product_id, :amount, :cost)");
            foreach ($products as $product) {
                $stmt->bindParam(':document_id', $documentId);
                $stmt->bindParam(':company_id', $data['company_id']);
                $stmt->bindParam(':product_id', $product['product_id']);
                $stmt->bindParam(':amount', $product['amount']);
                $stmt->bindParam(':cost', $product['cost']);
                $stmt->execute();
                $this->updateQuantity($product['product_id'], -$product['amount'],$data['company_id']);
            }

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function makeArrivalDocument($data) {
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO arrival_doc (company_id, seller_id, cost, pay_status, debt, time) 
                               VALUES (:company_id, :seller_id, :cost, :pay_status, :debt, NOW())");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':seller_id', $data["arrival"]["seller_id"]);
            $stmt->bindParam(':cost', $data["arrival"]["cost"]);
            $stmt->bindParam(':pay_status', $data["arrival"]["pay_status"]);
            $stmt->bindParam(':debt', $data["arrival"]["debt"]);
            $stmt->execute();
            $lastId = $con->lastInsertId();
            $this->makeArrivalProducts($lastId, $data);
            $result = array();
            $result["status"] = "done";
            echo json_encode($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function makeArrivalProducts($documentId, $data) {
        $db = new DBConnection();
        $products = $data["arrival"]["products"];
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO product_arrival (document_id, company_id, seller_id, product_id, category_id, amount, cost, sell_price, time) 
                               VALUES (:document_id, :company_id, :seller_id, :product_id, :category_id, :amount, :cost, :sell_price, NOW())");
            foreach ($products as $product) {
                $stmt->bindParam(':document_id', $documentId);
                $stmt->bindParam(':company_id', $data['company_id']);
                $stmt->bindParam(':seller_id', $data['arrival']['seller_id']);
                $stmt->bindParam(':product_id', $product['product_id']);
                $stmt->bindParam(':category_id', $product['category_id']);
                $stmt->bindParam(':amount', $product['amount']);
                $stmt->bindParam(':cost', $product['cost']);
                $stmt->bindParam(':sell_price', $product['sell_price']);
                $stmt->execute();
                $this->updateCostAndSellPrice($product['product_id'], $product['amount'],$product['cost'],$product['sell_price'], $data['company_id']);
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function updateCostAndSellPrice($productId, $quantity, $cost, $sellPrice, $companyId) {
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("SELECT quantity, cost FROM product WHERE id = :productId AND company_id = :companyId");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':companyId', $companyId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $currentQuantity = $result['quantity'];
            $currentCost = $result['cost'];

            $newCost = $currentCost;
            if ($currentQuantity > 0) {
                $totalQuantity = $currentQuantity + $quantity;
                $newCost = (($currentQuantity * $currentCost) + ($quantity * $cost)) / $totalQuantity;
            } elseif ($currentQuantity <= 0) {
                $newCost = $cost;
            }
            $stmt = $con->prepare("UPDATE product SET cost = :cost, selling_price = :sell_price,quantity = quantity + :quantity	WHERE id = :productId AND company_id = :companyId");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':cost', $newCost);
            $stmt->bindParam(':sell_price', $sellPrice);
            $stmt->bindParam(':companyId', $companyId);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}