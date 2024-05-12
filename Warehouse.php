<?php

class Warehouse
{
    public function makeSaleRecords($checkId, $products){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO sales (document_id, product_id, company_id, type, amount, tradespot, discount, price, time) 
                           VALUES (:document_id, :product_id, :company_id, :type, :amount, :tradespot, :discount, :price, NOW())");

            foreach ($products as $product) {
                $stmt->bindParam(':document_id', $checkId);
                $stmt->bindParam(':product_id', $product['product_id']);
                $stmt->bindParam(':company_id', $product['company_id']);
                $stmt->bindParam(':type', $product['type']);
                $stmt->bindParam(':amount', $product['amount']);
                $stmt->bindParam(':tradespot', $product['tradespot']);
                $stmt->bindParam(':discount', $product['discount']);
                $stmt->bindParam(':price', $product['price']);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function makeWriteOffDocument($data){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO write_off_doc (company_id, sum) 
                           VALUES (:company_id, :sum)");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':sum', $data["sum"]);
            $stmt->execute();
            return $con->lastInsertId(); // Return the ID of the newly inserted write-off document
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function makeWriteOffProducts($documentId, $products){
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $stmt = $con->prepare("INSERT INTO write_off_products (document_id, company_id, product_id, amount, cost) 
                           VALUES (:document_id, :company_id, :product_id, :amount, :cost)");

            foreach ($products as $product) {
                $stmt->bindParam(':document_id', $documentId);
                $stmt->bindParam(':company_id', $product['company_id']);
                $stmt->bindParam(':product_id', $product['product_id']);
                $stmt->bindParam(':amount', $product['amount']);
                $stmt->bindParam(':cost', $product['cost']);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}